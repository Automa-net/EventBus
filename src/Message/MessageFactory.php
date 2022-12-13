<?php

namespace AutomaNet\EventBus\Message;

use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\Message\IMessage;
use AutomaNet\EventBus\Contracts\Message\IMessageFactory;

class MessageFactory implements IMessageFactory
{
    private string $routingKeyPrefix;

    private string $projectName;

    /**
     * @param string $routingKeyPrefix
     * @param string $projectName
     */
    public function __construct(string $routingKeyPrefix, string $projectName)
    {
        $this->routingKeyPrefix = $routingKeyPrefix;
        $this->projectName = $projectName;
    }

    /**
     * @param EventInterface $event
     * @return IMessage
     */
    public function fromEvent(EventInterface $event): IMessage
    {
        return self::create(
            $event->getUuid(),
            $event->getName(),
            $event->getCreatedAt(),
            $event->getPayload()->toArray(),
            $this->projectName,
            $event->getRoutingKey() ? $event->getRoutingKey() : $this->createRoutingKey($event->getName())
        );
    }

    /**
     * @param string $uuid
     * @param string $eventName
     * @param \DateTimeImmutable $createdAt
     * @param array $payload
     * @param string $publishedBy
     * @param string $routingKey
     * @param \DateTimeImmutable|null $publishedAt
     * @return IMessage
     */
    public function create(string $uuid, string $eventName, \DateTimeImmutable $createdAt, array $payload, string $publishedBy, string $routingKey, ?\DateTimeImmutable $publishedAt = null): IMessage
    {
        return new Message(
            $uuid,
            $payload,
            $createdAt,
            $eventName,
            $routingKey,
            $publishedBy,
            $publishedAt
        );
    }

    /**
     * Automatically create routing key based on event name
     *
     * @param string $eventName
     * @return string
     */
    private function createRoutingKey(string $eventName): string
    {
        $chunks = preg_split('/(?=[A-Z])/', $eventName);
        return $this->routingKeyPrefix . '.' . implode('.', array_filter($chunks));
    }
}
