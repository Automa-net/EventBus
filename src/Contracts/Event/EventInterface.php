<?php

namespace AutomaNet\EventBus\Contracts\Event;

interface EventInterface
{
    public function getUuid(): string;

    public function getName(): string;

    public function getCreatedAt(): \DateTimeImmutable;

    public function getPayload(): EventPayloadInterface;
}
