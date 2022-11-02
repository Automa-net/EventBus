<?php

namespace AutomaNet\EventBus\Contracts\Event;

interface EventPayloadInterface
{
    public function toArray(): array;
}
