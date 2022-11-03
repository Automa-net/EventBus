<?php

namespace AutomaNet\EventBus\Driver\RabbitMq;

use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\IEventPublisher;
use AutomaNet\EventBus\Contracts\Message\IMessage;
use AutomaNet\EventBus\Contracts\Message\IMessageFactory;
use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqEventBusConnectionFactory;
use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqFactoryConnectable;
use AutomaNet\EventBus\Exceptions\MaxPublishAttemptsException;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMqEventBusPublisher implements IEventPublisher
{
    use RabbitMqFactoryConnectable;

    public const DEFAULT_MAX_PUBLISH_ATTEMPTS = 3;
    public const DEFAULT_NEXT_ATTEMPT_DELAY = 10;

    private int $nextAttemptDelay;

    private int $maxPublishAttempts;

    private string $exchange;

    private IMessageFactory $messageFactory;

    private int $publishAttempts = 0;

    public function __construct(
        RabbitMqEventBusConnectionFactory $connectionFactory,
        string                            $exchange,
        IMessageFactory                   $messageFactory,
        ?int $nextAttemptDelay = null,
        ?int $maxPublishAttempts = null
    ) {
        $this->connectionFactory = $connectionFactory;
        $this->exchange = $exchange;
        $this->messageFactory = $messageFactory;
        $this->nextAttemptDelay = $nextAttemptDelay ?? static::DEFAULT_NEXT_ATTEMPT_DELAY;
        $this->maxPublishAttempts = $maxPublishAttempts ?? static::DEFAULT_MAX_PUBLISH_ATTEMPTS;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function publish(EventInterface $event): void
    {
        $this->resetAttempts();

        $this->publishMessage($this->messageFactory->fromEvent($event));
    }

    /**
     * @param IMessage $message
     * @return void
     */
    public function publishMessage(IMessage $message): void
    {
        try {
            $this->increaseAttempts();

            $message->setPublishedAt(new \DateTimeImmutable('now'));

            $this->getChannel()->basic_publish($this->createAMQPMessage($message), $this->exchange, $message->getRoutingKey());
        } catch (AMQPConnectionClosedException $exception) {
            $this->assertNotMaxPublishAttemptsReached();

            if ($this->nextAttemptDelay > 0) {
                sleep($this->publishAttempts * $this->nextAttemptDelay);
            }

            $this->publishMessage($message);
        }
    }

    /**
     * @return void
     * @throws MaxPublishAttemptsException
     */
    private function assertNotMaxPublishAttemptsReached(): void
    {
        if ($this->publishAttempts >= $this->maxPublishAttempts) {
            throw new MaxPublishAttemptsException('Max publish attempts reached.');
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
     * @param IMessage $message
     * @return AMQPMessage
     */
    private function createAMQPMessage(IMessage $message): AMQPMessage
    {
        $headers = new AMQPTable($message->getHeaders());

        return new AMQPMessage(json_encode($message->getBody()), [
            'application_headers' => $headers,
            'content_type' => 'application/json',
        ]);
    }
}
