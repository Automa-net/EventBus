<?php

namespace AutomaNet\EventBus\Events;

use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\Event\EventPayloadInterface;

/**
 * @template TRawPayload of array
 */
abstract class Event implements EventInterface
{
    protected EventPayloadInterface $payload;

    protected string $uuid;

    protected \DateTimeImmutable $createdAt;

    protected ?string $routingKey = null;

    final public function __construct(string $uuid, \DateTimeImmutable $createdAt, EventPayloadInterface $payload)
    {
        $this->uuid = $uuid;
        $this->createdAt = $createdAt;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }

    /**
     * @return EventPayloadInterface
     */
    public function getPayload(): EventPayloadInterface
    {
        return $this->payload;
    }

    /**
     * @param TRawPayload $payload
     * @return static
     * @throws \Exception
     */
    final public static function newFromArray(array $payload): self
    {
        return EventFactory::createNew(static::class, $payload);
    }

    /**
     * Get custom routing key
     *
     * @return string|null
     */
    public function getRoutingKey(): ?string
    {
        return $this->routingKey;
    }

    /**
     * Set custom routing key
     *
     * @param string|null $routingKey
     */
    public function setRoutingKey(?string $routingKey): void
    {
        $this->routingKey = $routingKey;
    }
}
