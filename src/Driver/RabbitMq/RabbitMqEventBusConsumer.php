<?php

namespace AutomaNet\EventBus\Driver\RabbitMq;

use AutomaNet\EventBus\Contracts\Dispatcher\IEventMessageDispatcher;
use AutomaNet\EventBus\Contracts\IEventConsumer;
use AutomaNet\EventBus\Contracts\Message\IMessage;
use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqEventBusConnectionFactory;
use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqFactoryConnectable;
use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqHasHeartbeatSender;
use AutomaNet\EventBus\Driver\RabbitMq\Contracts\MessageFactoryInterface;
use AutomaNet\EventBus\Exceptions\MaxPublishAttemptsException;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqEventBusConsumer implements IEventConsumer
{
    use RabbitMqFactoryConnectable, RabbitMqHasHeartbeatSender;
    public const DEFAULT_MAX_CONSUME_ATTEMPTS = 3;
    public const DEFAULT_NEXT_ATTEMPT_DELAY = 10;

    private int $maxConsumeAttempts;

    private int $nextAttemptDelay;

    private string $queue;

    private IEventMessageDispatcher $messageDispatcher;

    private MessageFactoryInterface $messageFactory;

    private int $consumeAttempts = 0;

    public function __construct(
        RabbitMqEventBusConnectionFactory $connectionFactory,
        string                            $queue,
        IEventMessageDispatcher           $messageDispatcher,
        MessageFactoryInterface           $messageFactory,
        bool $enableHeartbeatSender = false,
        ?int $maxConsumeAttempts = null,
        ?int $nextAttemptDelay = null
    ) {
        $this->connectionFactory = $connectionFactory;
        $this->queue = $queue;
        $this->messageDispatcher = $messageDispatcher;
        $this->messageFactory = $messageFactory;
        $this->enableHeartbeatSender = $enableHeartbeatSender;
        $this->maxConsumeAttempts = $maxConsumeAttempts ?? self::DEFAULT_MAX_CONSUME_ATTEMPTS;
        $this->nextAttemptDelay = $nextAttemptDelay ?? self::DEFAULT_NEXT_ATTEMPT_DELAY;
    }

    public function consume(): void
    {
        $this->resetAttempts();

        $this->consumeMessages();
    }

    private function consumeMessages(): void
    {
        try {
            $channel = $this->getChannel();

            $this->increaseAttempts();

            $channel->basic_consume($this->queue, '', false, false, false, false, [$this, 'processMessage']);

            $this->resetAttempts();

            $channel->consume();
        } catch (AMQPConnectionClosedException $exception) {
            $this->assertNotMaxConsumeAttemptsReached();

            if ($this->nextAttemptDelay > 0) {
                sleep($this->consumeAttempts * $this->nextAttemptDelay);
            }

            $this->consume();
        }
    }

    /**
     * @return void
     * @throws MaxPublishAttemptsException
     */
    private function assertNotMaxConsumeAttemptsReached(): void
    {
        if ($this->consumeAttempts >= $this->maxConsumeAttempts) {
            throw new MaxPublishAttemptsException('Max publish attempts reached');
        }
    }

    /**
     * @return void
     */
    private function resetAttempts(): void
    {
        $this->consumeAttempts = 0;
    }

    /**
     * @return void
     */
    private function increaseAttempts(): void
    {
        $this->consumeAttempts++;
    }

    /**
     * @param AMQPMessage $AMQPMessage
     * @return void
     */
    public function processMessage(AMQPMessage $AMQPMessage)
    {
        $this->dispatch($this->messageFactory->fromAMQPMessage($AMQPMessage));

        $AMQPMessage->ack();
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
