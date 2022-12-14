<?php

namespace AutomaNet\EventBus\Examples\Fixtures\Event;

use AutomaNet\EventBus\Contracts\Event\EventPayloadInterface;
use AutomaNet\EventBus\Events\Event;

class ProductUpdated extends Event {

    /**
     * @var ProductUpdatedPayload
     */
    protected EventPayloadInterface $payload;

    public function getPayload(): ProductUpdatedPayload
    {
        return $this->payload;
    }
}