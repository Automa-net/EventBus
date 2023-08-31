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

class RabbitMqEventBusConsumer implements IEventConsumer
{
    use RabbitMqFactoryConnectable, RabbitMqHasHeartbeatSender;

    private RabbitMqConsumerConfig $config;

    private IEventMessageDispatcher $messageDispatcher;

    private MessageFactoryInterface $messageFactory;

    private LoggerInterface $logger;

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
        $this->enableHeartbeatSender = $consumerConfig->isEnableHeartbeatSender();
    }

    public function consume(): void
    {
        $this->consumeMessages();
    }

    private function consumeMessages(): void
    {
        $channel = $this->getChannel();

        $this->logger->notice('Start consuming messages');

        $channel->basic_qos(null, $this->config->getPrefetchCount(), null); /** @phpstan-ignore-line */
        $channel->basic_consume($this->config->getQueue(), '', false, false, false, false, [$this, 'processMessage']);

        $channel->consume();
    }

    /**
     * @param AMQPMessage $AMQPMessage
     * @return void
     */
    public function processMessage(AMQPMessage $AMQPMessage)
    {
        try {
            $this->dispatch($this->messageFactory->fromAMQPMessage($AMQPMessage));

            $AMQPMessage->ack();
        } catch (\Error|\Exception $e) {
            $this->logger->error($e);
            $AMQPMessage->nack();
        }
    }

    /**
     * @param IMessage $message
     * @return void
     */
    public function dispatch(IMessage $message): void
    {
        $this->messageDispatcher->dispatchMessage($message);
    }
}
