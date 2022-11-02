<?php

namespace AutomaNet\EventBus\Examples\Fixtures\Event;

use AutomaNet\EventBus\Contracts\Event\EventPayloadInterface;
use AutomaNet\EventBus\Events\Event;

class ProductCreated extends Event
{
    /**
     * @var ProductCreatedPayload
     */
    protected EventPayloadInterface $payload;

    public function getPayload(): ProductCreatedPayload
    {
        return $this->payload;
    }
}