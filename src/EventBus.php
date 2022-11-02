<?php

namespace AutomaNet\EventBus;

use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;
use AutomaNet\EventBus\Contracts\IEventPublisher;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;

class EventBus implements EventBusInterface
{
    private IEventPublisher $publisher;

    private EventBusSubscriptionManagerInterface $subscriptionManager;

    public function __construct(IEventPublisher $publisher, EventBusSubscriptionManagerInterface $subscriptionManager)
    {
        $this->publisher = $publisher;
        $this->subscriptionManager = $subscriptionManager;
    }

    public function subscribe($eventListenerClassName, ?int $priority = 100, ?string $connection = null): void
    {
        $this->subscriptionManager->registerSubscriber($eventListenerClassName, $priority);
    }

    public function publish(array $events, ?string $connection = null): void
    {
        foreach ($events as $domainEvent) {
            $this->publisher->publish($domainEvent);
        }
    }
}
