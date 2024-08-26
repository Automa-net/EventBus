<?php

namespace AutomaNet\EventBus\Contracts\Message;

interface IMessage
{
    public const HEADER_UUID_KEY = 'uuid';
    public const HEADER_EVENT_NAME_KEY = 'event_name';
    public const HEADER_CREATED_AT = 'created_at';
    public const HEADER_PUBLISHED_AT = 'published_at';
    public const HEADER_PUBLISHED_BY = 'published_by';

    public function getUuid(): string;

    public function getCreatedAt(): \DateTimeImmutable;

    /**
     * @return array<int|string, mixed>
     */
    public function getBody(): array;

    public function getHeaders(): array;

    public function getEventName(): string;

    public function getPublishedAt(): ?\DateTimeImmutable;

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): void;

    public function getPublishedBy(): string;

    public function getRoutingKey(): string;
}
