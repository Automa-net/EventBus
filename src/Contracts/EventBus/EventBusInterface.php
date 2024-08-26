<?php

namespace AutomaNet\EventBus\Contracts\EventBus;

use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventSubscriberInterface;

interface EventBusInterface
{
    /**
     * @param class-string<EventSubscriberInterface> $eventSubscriberClassName
     * @param int $priority
     * @return void
     */
    public function subscribe(string $eventSubscriberClassName, int $priority = 100): void;

    /**
     * @param class-string<EventSubscriberInterface> $eventSubscriberClassName
     * @param int $priority
     * @return void
     */
    public function unsubscribe(string $eventSubscriberClassName, int $priority = 100);

    /**
     * @param EventInterface[] $events
     * @return void
     */
    public function publish(array $events): void;

    /**
     * @return EventBusSubscriptionManagerInterface
     */
    public function getSubscriptionManager(): EventBusSubscriptionManagerInterface;
}
