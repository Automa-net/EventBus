<?php

namespace AutomaNet\EventBus\Contracts\Event;

interface EventPayloadInterface
{
    /**
     * @param array<int|string, mixed> $data
     */
    public function __construct(array $data);

    /**
     * @return array<int|string, mixed>
     */
    public function toArray(): array;
}
