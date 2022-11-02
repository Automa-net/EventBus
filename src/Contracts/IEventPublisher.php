<?php

namespace AutomaNet\EventBus\Contracts;

use AutomaNet\EventBus\Contracts\Event\EventInterface;

interface IEventPublisher
{
    /**
     * Publish and event
     *
     * @param EventInterface $event
     * @return void
     */
    public function publish(EventInterface $event): void;
}
