<?php

namespace AutomaNet\EventBus\Contracts\Subscription;

use AutomaNet\EventBus\Reflection\ReflectionEventHandler;

interface EventBusSubscriptionManagerInterface
{
    /**
     * @param string $subscriber
     * @param int $priority
     * @return void
     * @throws \Exception
     */
    public function registerSubscriber(string $subscriber, int $priority);

    /**
     * @param string $eventName
     * @param ReflectionEventHandler $reflectionEventHandler
     * @param int $priority
     * @return void
     */
    public function registerHandler(string $eventName, ReflectionEventHandler $reflectionEventHandler, int $priority);

    /**
     * @param string $eventName
     * @return ReflectionEventHandler[]
     */
    public function getHandlersByName(string $eventName): array;
}
