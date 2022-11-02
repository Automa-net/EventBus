<?php

namespace AutomaNet\EventBus\Contracts\Event;

interface EventPayloadInterface
{
    /**
     * @param array $data
     */
    public function __construct(array $data);

    /**
     * @return array
     */
    public function toArray(): array;
}
