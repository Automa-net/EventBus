<?php

namespace AutomaNet\EventBus;

use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;
use AutomaNet\EventBus\Contracts\IEventPublisher;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;

class EventBus implements EventBusInterface
{
    private IEventPublisher $publisher;

    private EventBusSubscriptionManagerInterface $subscriptionManager;

    /**
     * @param IEventPublisher $publisher
     * @param EventBusSubscriptionManagerInterface $subscriptionManager
     */
    public function __construct(IEventPublisher $publisher, EventBusSubscriptionManagerInterface $subscriptionManager)
    {
        $this->publisher = $publisher;
        $this->subscriptionManager = $subscriptionManager;
    }

    /**
     * @param $eventListenerClassName
     * @param int|null $priority
     * @param string|null $connection
     * @return void
     * @throws \Exception
     */
    public function subscribe($eventListenerClassName, ?int $priority = 100, ?string $connection = null): void
    {
        $this->subscriptionManager->registerSubscriber($eventListenerClassName, $priority);
    }

    /**
     * @param array $events
     * @param string|null $connection
     * @return void
     */
    public function publish(array $events, ?string $connection = null): void
    {
        foreach ($events as $domainEvent) {
            $this->publisher->publish($domainEvent);
        }
    }

    /**
     * @return EventBusSubscriptionManagerInterface
     */
    public function getSubscriptionManager(): EventBusSubscriptionManagerInterface
    {
        return $this->subscriptionManager;
    }
}
