<?php

namespace AutomaNet\EventBus;

use AutomaNet\EventBus\Contracts\EventBusManagerInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionManager;

class EventBusManager implements EventBusManagerInterface
{
    private array $subscriptionManagers = [];

    private array $eventBuses = [];

    private EventBusFactory $eventBusFactory;

    private string $defaultConnection;

    public function __construct(EventBusFactory $eventBusFactory, string $defaultConnection)
    {
        $this->eventBusFactory = $eventBusFactory;
        $this->defaultConnection = $defaultConnection;
    }

    public function subscribe($eventListenerClassName, ?int $priority = 100, ?string $connection = null): void
    {
        $this->getEventBus($connection)->subscribe($eventListenerClassName, $priority);
    }

    public function publish(array $events, ?string $connection = null): void
    {
        $this->getEventBus($connection)->publish($events);
    }

    public function getEventBus(?string $connection = null): EventBus
    {
        $connection = $connection ?? $this->defaultConnection;

        if (!isset($this->eventBuses[$connection])) {
            $this->eventBuses[$connection] = $this->eventBusFactory->create($connection, $this->getSubscriptionManager($connection));
        }

        return $this->eventBuses[$connection];
    }

    private function getSubscriptionManager(?string $connection = null): EventBusSubscriptionManagerInterface
    {
        if (!isset($this->subscriptionManagers[$connection])) {
            $this->subscriptionManagers[$connection] = new EventBusSubscriptionManager();
        }

        return $this->subscriptionManagers[$connection];
    }

    public function getSubscriptionManagers(): array
    {
        return $this->subscriptionManagers;
    }
}
