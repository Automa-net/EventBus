<?php

namespace AutomaNet\EventBus\Contracts\EventBus;

use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;

interface EventBusFactoryInterface
{
    public function create(array $config, EventBusSubscriptionManagerInterface $subscriptionManager): EventBusInterface;
}
