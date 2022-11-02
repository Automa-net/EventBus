<?php

namespace AutomaNet\EventBus\Contracts\Subscription;

use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Reflection\ReflectionEventHandler;

interface SubscriptionHandlerResolverInterface
{
    public function resolve(ReflectionEventHandler $eventHandler): callable;

    public function __invoke(ReflectionEventHandler $eventHandler, EventInterface $event): void;
}
