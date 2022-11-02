<?php

namespace AutomaNet\EventBus\Subscription;

use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\Subscription\SubscriptionHandlerResolverInterface;
use AutomaNet\EventBus\Reflection\ReflectionEventHandler;
use Psr\Container\ContainerInterface;

class EventBusSubscriptionHandlerResolver implements SubscriptionHandlerResolverInterface
{
    private ContainerInterface $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve(ReflectionEventHandler $eventHandler): callable
    {
        return $eventHandler->getClosure($this->container->get($eventHandler->getListenerClass()));
    }

    public function __invoke(ReflectionEventHandler $eventHandler, EventInterface $event): void
    {
        $this->resolve($eventHandler)($event);
    }
}
