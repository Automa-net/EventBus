<?php

namespace AutomaNet\EventBus\Message;

use AutomaNet\EventBus\Contracts\Message\IMessage;

class Message implements IMessage
{
    private string $uuid;

    private array $body;

    private \DateTimeImmutable $createdAt;

    private string $eventName;

    private string $routingKey;

    private string $publishedBy;

    private ?\DateTimeImmutable $publishedAt;

    public function __construct(
        string $uuid,
        array $body,
        \DateTimeImmutable $createdAt,
        string $eventName,
        string $routingKey,
        string $publishedBy,
        ?\DateTimeImmutable $publishedAt = null
    )
    {
        $this->uuid = $uuid;
        $this->body = $body;
        $this->createdAt = $createdAt;
        $this->eventName = $eventName;
        $this->routingKey = $routingKey;
        $this->publishedBy = $publishedBy;
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
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
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return string
     */
    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    /**
     * @return string
     */
    public function getPublishedBy(): string
    {
        return $this->publishedBy;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTimeImmutable|null $publishedAt
     */
    public function setPublishedAt(?\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * Returns message headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            IMessage::HEADER_UUID_KEY => $this->getUuid(),
            IMessage::HEADER_EVENT_NAME_KEY => $this->getEventName(),
            IMessage::HEADER_CREATED_AT => $this->getCreatedAt()->format(\DateTimeInterface::ATOM),
            IMessage::HEADER_PUBLISHED_BY => $this->publishedBy,
            IMessage::HEADER_PUBLISHED_AT => $this->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
