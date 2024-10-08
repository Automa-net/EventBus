<?php

namespace AutomaNet\EventBus\Driver\Direct;

use AutomaNet\EventBus\Contracts\Event\EventFactoryInterface;
use AutomaNet\EventBus\Contracts\EventBus\EventBusFactoryInterface;
use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;
use AutomaNet\EventBus\Contracts\Subscription\SubscriptionHandlerResolverInterface;
use AutomaNet\EventBus\Dispatchers\EventDispatcher;
use AutomaNet\EventBus\EventBus;
use AutomaNet\EventBus\Events\EventFactory;

/**
 * @phpstan-type DirectEventBusFactoryConfig array{
 *     driver: "direct",
 * }
 */
class DirectEventBusFactory implements EventBusFactoryInterface
{
    private SubscriptionHandlerResolverInterface $subscriptionHandlerResolver;

    private EventFactoryInterface $eventFactory;

    /**
     * @param SubscriptionHandlerResolverInterface $subscriptionHandlerResolver
     * @param ?EventFactoryInterface $eventFactory
     */
    public function __construct(SubscriptionHandlerResolverInterface $subscriptionHandlerResolver, ?EventFactoryInterface $eventFactory = null)
    {
        $this->subscriptionHandlerResolver = $subscriptionHandlerResolver;
        $this->eventFactory = $eventFactory ?? new EventFactory();
    }

    /**
     * @param DirectEventBusFactoryConfig $config
     * @param EventBusSubscriptionManagerInterface $subscriptionManager
     * @return EventBusInterface
     */
    public function create(array $config, EventBusSubscriptionManagerInterface $subscriptionManager): EventBusInterface
    {
        return new EventBus(
            new DirectEventBusPublisher(new EventDispatcher(
                $subscriptionManager,
                $this->subscriptionHandlerResolver,
                $this->eventFactory,
            )),
            $subscriptionManager
        );
    }
}
