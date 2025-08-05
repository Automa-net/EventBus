<?php

namespace AutomaNet\EventBus\Driver\RabbitMq;

use AutomaNet\EventBus\Contracts\Dispatcher\IEventMessageDispatcher;
use AutomaNet\EventBus\Contracts\IEventConsumer;
use AutomaNet\EventBus\Contracts\Message\IMessage;
use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqEventBusConnectionFactory;
use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqFactoryConnectable;
use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqHasHeartbeatSender;
use AutomaNet\EventBus\Driver\RabbitMq\Contracts\MessageFactoryInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * RabbitMQ-based event consumer that processes messages from a queue.
 *
 * This consumer handles:
 * - Message consumption from RabbitMQ queues
 * - Retry logic with configurable maximum attempts
 * - Parking lot queue for failed messages
 * - Heartbeat sending to maintain connection health
 * - Proper message acknowledgment and error handling
 *
 * Uses RabbitMQ's dead letter exchange mechanism for retry handling.
 */
class RabbitMqEventBusConsumer implements IEventConsumer
{
    use RabbitMqFactoryConnectable, RabbitMqHasHeartbeatSender;

    /** @var RabbitMqConsumerConfig Consumer configuration including queue settings and retry limits */
    private RabbitMqConsumerConfig $config;

    /** @var IEventMessageDispatcher Dispatcher responsible for routing messages to event handlers */
    private IEventMessageDispatcher $messageDispatcher;

    /** @var MessageFactoryInterface Factory for converting AMQP messages to internal message format */
    private MessageFactoryInterface $messageFactory;

    /** @var LoggerInterface Logger for error reporting and debugging information */
    private LoggerInterface $logger;

    /**
     * Initializes the RabbitMQ event consumer with required dependencies.
     *
     * @param RabbitMqEventBusConnectionFactory $connectionFactory Factory for creating RabbitMQ connections
     * @param RabbitMqConsumerConfig $consumerConfig Configuration for consumer behavior (queue, retries, etc.)
     * @param IEventMessageDispatcher $messageDispatcher Dispatcher to route messages to event handlers
     * @param MessageFactoryInterface $messageFactory Factory to convert AMQP messages to internal format
     * @param LoggerInterface $logger Logger for error reporting and debugging
     */
    public function __construct(
        RabbitMqEventBusConnectionFactory $connectionFactory,
        RabbitMqConsumerConfig            $consumerConfig,
        IEventMessageDispatcher           $messageDispatcher,
        MessageFactoryInterface           $messageFactory,
        LoggerInterface                   $logger
    ) {
        $this->connectionFactory = $connectionFactory;
        $this->config = $consumerConfig;
        $this->messageDispatcher = $messageDispatcher;
        $this->messageFactory = $messageFactory;
        $this->logger = $logger;
        // Enable heartbeat sender if configured to maintain connection health
        $this->enableHeartbeatSender = $consumerConfig->isEnableHeartbeatSender();
    }

    /**
     * Starts consuming messages from the configured RabbitMQ queue.
     *
     * This method blocks and continuously processes incoming messages
     * until the connection is closed or an error occurs.
     *
     * @return void
     */
    public function consume(): void
    {
        $this->consumeMessages();
    }

    /**
     * Sets up the RabbitMQ channel and starts the message consumption loop.
     *
     * Configures Quality of Service (QoS) settings and registers the message
     * processing callback before entering the blocking consume loop.
     *
     * @return void
     */
    private function consumeMessages(): void
    {
        $channel = $this->getChannel();

        $this->logger->notice('Start consuming messages');

        // Set QoS to limit unacknowledged messages per consumer
        $channel->basic_qos(0, $this->config->getPrefetchCount(), false);

        // Register consumer callback - processMessage will be called for each message
        $channel->basic_consume(
            $this->config->getQueue(),      // Queue name
            $this->config->getConsumerTag(), // Consumer tag
            false,                          // No local delivery
            false,                          // Manual acknowledgment
            false,                          // Not exclusive
            false,                          // No wait
            [$this, 'processMessage']       // Callback function
        );

        // Start the blocking consume loop
        $channel->consume();
    }

    /**
     * Processes an incoming AMQP message from the queue.
     *
     * Handles retry logic by checking if the message has exceeded the maximum retry attempts.
     * If so, sends the message to a parking lot queue (if configured) and acknowledges it.
     * Otherwise, attempts to dispatch the message to the appropriate event handler.
     *
     * @param AMQPMessage $AMQPMessage The AMQP message to process
     * @return void
     */
    public function processMessage(AMQPMessage $AMQPMessage): void
    {
        // Check if message has been retried too many times
        if ($this->hasExceededRetryLimit($AMQPMessage)) {
            $this->handleExceededRetryLimit($AMQPMessage);
            return;
        }

        // Process the message normally
        $this->processMessageDispatch($AMQPMessage);
    }

    /**
     * Extracts the retry count from the AMQP message headers.
     *
     * RabbitMQ tracks failed message attempts in the 'x-death' header.
     * This method examines all death records and returns the highest count.
     *
     * @param AMQPMessage $AMQPMessage The AMQP message to examine
     * @return int The number of times this message has been retried
     */
    private function getRetryCount(AMQPMessage $AMQPMessage): int
    {
        $headers = $AMQPMessage->get('application_headers');
        // RabbitMQ stores retry information in the 'x-death' header
        $xDeath = $headers->getNativeData()['x-death'] ?? [];

        $retryCount = 0;

        // Find the maximum retry count from all death records
        foreach ($xDeath as $death) {
            $retryCount = max($retryCount, $death['count']);
        }

        return $retryCount;
    }

    /**
     * Determines if the message has exceeded the configured retry limit.
     *
     * @param AMQPMessage $AMQPMessage The AMQP message to check
     * @return bool True if the message has exceeded the retry limit, false otherwise
     */
    private function hasExceededRetryLimit(AMQPMessage $AMQPMessage): bool
    {
        return $this->getRetryCount($AMQPMessage) > $this->config->getMaxRetries();
    }

    /**
     * Handles messages that have exceeded the retry limit.
     *
     * Moves the message to a parking lot queue (if configured) for manual inspection
     * and acknowledges the message to remove it from the main queue.
     *
     * @param AMQPMessage $AMQPMessage The message that exceeded retry limits
     * @return void
     */
    private function handleExceededRetryLimit(AMQPMessage $AMQPMessage): void
    {
        // Move message to parking lot for manual inspection
        $this->sendToParkingLot($AMQPMessage);

        // Acknowledge to remove from main queue (message is now in parking lot or discarded)
        $AMQPMessage->ack();
    }

    /**
     * Sends a failed message to the parking lot queue for manual inspection.
     *
     * If no parking lot queue is configured, the message will be discarded.
     * This allows administrators to examine messages that consistently fail processing.
     *
     * @param AMQPMessage $AMQPMessage The message to send to the parking lot
     * @return void
     */
    private function sendToParkingLot(AMQPMessage $AMQPMessage): void
    {
        $parkingLotQueueName = $this->config->getParkingLotQueueName();

        // Only send to parking lot if one is configured
        if ($parkingLotQueueName) {
            $this->getChannel()->basic_publish(
                $AMQPMessage,
                '', // No exchange - direct to queue
                $parkingLotQueueName
            );
        }
        // If no parking lot is configured, message is effectively discarded
    }

    /**
     * Attempts to dispatch the message to the appropriate event handler.
     *
     * Converts the AMQP message to the internal message format and dispatches it.
     * On success, acknowledges the message. On failure, logs the error and
     * negative-acknowledges the message (causing it to be requeued for retry).
     *
     * @param AMQPMessage $AMQPMessage The AMQP message to process
     * @return void
     */
    private function processMessageDispatch(AMQPMessage $AMQPMessage): void
    {
        try {
            // Convert AMQP message to internal message format
            $message = $this->messageFactory->fromAMQPMessage($AMQPMessage);

            // Dispatch to the appropriate event handler
            $this->dispatch($message);

            // Message processed successfully - acknowledge to remove from queue
            $AMQPMessage->ack();
        } catch (\Error|\Exception $e) {
            // Log the error for debugging
            $this->logger->error($e);

            // Negative acknowledge - message will be requeued for retry
            $AMQPMessage->nack();
        }
    }

    /**
     * Dispatches the message to the registered event handlers.
     *
     * This is a simple delegation to the message dispatcher, which handles
     * finding and invoking the appropriate event subscribers.
     *
     * @param IMessage $message The message to dispatch
     * @return void
     */
    private function dispatch(IMessage $message): void
    {
        $this->messageDispatcher->dispatchMessage($message);
    }
}
