<?php

namespace AutomaNet\EventBus\Contracts\Message;

use AutomaNet\EventBus\Contracts\Event\EventInterface;

interface IMessageFactory
{
    public function fromEvent(EventInterface $event): IMessage;

    public function create(string $uuid, string $eventName, \DateTimeImmutable $createdAt, array $payload, string $publishedBy, string $routingKey, ?\DateTimeImmutable $publishedAt): IMessage;
}
