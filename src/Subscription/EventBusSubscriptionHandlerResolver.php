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

    /**
     * @param ReflectionEventHandler $eventHandler
     * @return callable
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function resolve(ReflectionEventHandler $eventHandler): callable
    {
        return $eventHandler->getClosure($this->getSubscriberInstance($eventHandler->getSubscriberClass()));
    }

    /**
     * @param string $className
     * @return EventSubscriber
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function getSubscriberInstance(string $className): EventSubscriber
    {
        return $this->container->get($className);
    }

    /**
     * @param ReflectionEventHandler $eventHandler
     * @param EventInterface $event
     * @return void
     */
    public function __invoke(ReflectionEventHandler $eventHandler, EventInterface $event): void
    {
        $this->resolve($eventHandler)($event);
    }
}
