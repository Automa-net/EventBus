<?php

namespace AutomaNet\EventBus\Contracts\EventBus;

interface EventBusInterface
{
    /**
     * Subscribes the event listener to the event bus.
     */
    public function subscribe($eventListenerClassName, int $priority): void;

    /**
     * Publishes the events from the domain event stream to the listeners.
     */
    public function publish(array $events): void;
}
