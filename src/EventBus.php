<?php

namespace AutomaNet\EventBus;

use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;
use AutomaNet\EventBus\Contracts\IEventPublisher;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventSubscriberInterface;

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
     * @param class-string<EventSubscriberInterface> $eventSubscriberClassName
     * @param int $priority
     * @return void
     * @throws \Exception
     */
    public function subscribe(string $eventSubscriberClassName, int $priority = 100): void
    {
        $this->subscriptionManager->registerSubscriber($eventSubscriberClassName, $priority);
    }

    /**
     * @param class-string<EventSubscriberInterface> $eventSubscriberClassName
     * @param int $priority
     * @return void
     * @throws \Exception
     */
    public function unsubscribe(string $eventSubscriberClassName, int $priority = 100)
    {
        $this->subscriptionManager->unregisterSubscriber($eventSubscriberClassName, $priority);
    }

    /**
     * @param EventInterface[] $events
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
