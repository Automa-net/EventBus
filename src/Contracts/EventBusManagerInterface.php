<?php

namespace AutomaNet\EventBus\Contracts;

use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventSubscriberInterface;

interface EventBusManagerInterface extends EventBusInterface
{
    /**
     * @param class-string<EventSubscriberInterface> $eventSubscriberClassName
     * @param int $priority
     * @param string|null $connection
     * @return void
     */
    public function subscribe(string $eventSubscriberClassName, int $priority = 100, ?string $connection = null): void;

    /**
     * @param class-string<EventSubscriberInterface> $eventSubscriberClassName
     * @param int $priority
     * @param string|null $connection
     * @return void
     */
    public function unsubscribe(string $eventSubscriberClassName, int $priority = 100, ?string $connection = null): void;

    /**
     * @param EventInterface[] $events
     * @param string|null $connection
     * @return void
     */
    public function publish(array $events, ?string $connection = null): void;

    /**
     * @param string|null $connection
     * @return EventBusInterface
     */
    public function getEventBus(?string $connection = null): EventBusInterface;
}
