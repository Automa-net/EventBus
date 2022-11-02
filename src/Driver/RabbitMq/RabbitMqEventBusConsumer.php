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
    public const MAX_CONSUME_ATTEMPTS = 3;
    public const NEXT_ATTEMPT_DELAY = 10;

    private string $queue;

    private IEventMessageDispatcher $messageDispatcher;

    private MessageFactoryInterface $messageFactory;

    private int $publishAttempts = 0;

    public function __construct(
        RabbitMqEventBusConnectionFactory $connectionFactory,
        string                            $queue,
        IEventMessageDispatcher           $messageDispatcher,
        MessageFactoryInterface           $messageFactory
    )
    {
        $this->connectionFactory = $connectionFactory;
        $this->queue = $queue;
        $this->messageDispatcher = $messageDispatcher;
        $this->messageFactory = $messageFactory;
    }

    public function consume(): void
    {
        try {
            $channel = $this->getChannel();

            $channel->basic_consume($this->queue, '', false, false, false, false, [$this, 'processMessage']);

            while ($this->isConnectionOpen()) {
                $this->resetAttempts();

                $channel->consume();
            }
        } catch (AMQPConnectionClosedException $exception) {
            $this->assertNotMaxConsumeAttemptsReached();

            $this->increaseAttempts();

            sleep($this->publishAttempts * self::NEXT_ATTEMPT_DELAY);

            $this->consume();
        }
    }

    /**
     * @return void
     * @throws MaxPublishAttemptsException
     */
    private function assertNotMaxConsumeAttemptsReached(): void
    {
        if ($this->publishAttempts >= self::MAX_CONSUME_ATTEMPTS) {
            throw new MaxPublishAttemptsException('Max publish attempts reached');
        }
    }

    /**
     * @return void
     */
    private function resetAttempts(): void
    {
        $this->publishAttempts = 0;
    }

    /**
     * @return void
     */
    private function increaseAttempts(): void
    {
        $this->publishAttempts++;
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
