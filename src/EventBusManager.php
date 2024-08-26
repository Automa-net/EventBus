<?php

namespace AutomaNet\EventBus;

use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;
use AutomaNet\EventBus\Contracts\EventBusManagerInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventSubscriberInterface;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionManager;

class EventBusManager implements EventBusManagerInterface
{
    /**
     * @var EventBusSubscriptionManagerInterface[]
     */
    private array $subscriptionManagers = [];

    /**
     * @var EventBusInterface[]
     */
    private array $eventBuses = [];

    private EventBusFactory $eventBusFactory;

    private string $defaultConnection;

    public function __construct(EventBusFactory $eventBusFactory, string $defaultConnection)
    {
        $this->eventBusFactory = $eventBusFactory;
        $this->defaultConnection = $defaultConnection;
    }

    /**
     * @param class-string<EventSubscriberInterface> $eventSubscriberClassName
     * @param int $priority
     * @param string|null $connection
     * @return void
     */
    public function subscribe(string $eventSubscriberClassName, int $priority = 100, ?string $connection = null): void
    {
        $this->getEventBus($connection)->subscribe($eventSubscriberClassName, $priority);
    }

    /**
     * @param class-string<EventSubscriberInterface> $eventSubscriberClassName
     * @param int $priority
     * @param string|null $connection
     * @return void
     */
    public function unsubscribe(string $eventSubscriberClassName, int $priority = 100, ?string $connection = null): void {
        $this->getEventBus($connection)->unsubscribe($eventSubscriberClassName, $priority);
    }

    /**
     * @param EventInterface[] $events
     * @param string|null $connection
     * @return void
     */
    public function publish(array $events, ?string $connection = null): void
    {
        $this->getEventBus($connection)->publish($events);
    }

    public function getEventBus(?string $connection = null): EventBusInterface
    {
        $connection = $connection ?? $this->defaultConnection;

        if (!isset($this->eventBuses[$connection])) {
            $this->eventBuses[$connection] = $this->eventBusFactory->create($connection, $this->getSubscriptionManager($connection));
        }

        return $this->eventBuses[$connection];
    }

    public function getSubscriptionManager(?string $connection = null): EventBusSubscriptionManagerInterface
    {
        if (!isset($this->subscriptionManagers[$connection])) {
            $this->subscriptionManagers[$connection] = new EventBusSubscriptionManager();
        }

        return $this->subscriptionManagers[$connection];
    }

    /**
     * @return EventBusSubscriptionManagerInterface[]
     */
    public function getSubscriptionManagers(): array
    {
        return $this->subscriptionManagers;
    }
}
