<?php

namespace AutomaNet\EventBus\Contracts\Event;

interface EventInterface
{
    /**
     * @return string
     */
    public function getUuid(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable;

    /**
     * @return EventPayloadInterface
     */
    public function getPayload(): EventPayloadInterface;

    /**
     * Get custom routing key
     *
     * @return string|null
     */
    public function getRoutingKey(): ?string;

    /**
     * Set custom routing key
     *
     * @param string|null $routingKey
     */
    public function setRoutingKey(?string $routingKey): void;
}
