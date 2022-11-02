<?php

namespace AutomaNet\EventBus\Examples\Fixtures\Event;

use AutomaNet\EventBus\Events\Event;

class ProductUpdated extends Event {

    /**
     * @var ProductUpdatedEventPayload
     */
    protected \AutomaNet\EventBus\Contracts\Event\EventPayloadInterface $payload;

    public function getPayload(): ProductUpdatedEventPayload
    {
        return $this->payload;
    }
}