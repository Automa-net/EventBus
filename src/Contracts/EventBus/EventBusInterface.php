<?php

namespace AutomaNet\EventBus\Contracts\EventBus;

use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;

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

    /**
     * @return EventBusSubscriptionManagerInterface
     */
    public function getSubscriptionManager(): EventBusSubscriptionManagerInterface;
}
