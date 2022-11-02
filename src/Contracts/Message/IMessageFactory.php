<?php

namespace AutomaNet\EventBus\Contracts\Message;

use AutomaNet\EventBus\Contracts\Event\EventInterface;

interface IMessageFactory
{
    /**
     * @param EventInterface $event
     * @return IMessage
     */
    public function fromEvent(EventInterface $event): IMessage;

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
    public function create(string $uuid, string $eventName, \DateTimeImmutable $createdAt, array $payload, string $publishedBy, string $routingKey, ?\DateTimeImmutable $publishedAt): IMessage;
}
