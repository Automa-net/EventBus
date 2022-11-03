<?php

namespace AutomaNet\EventBus\Tests\Unit\Driver\Direct;

use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;
use AutomaNet\EventBus\Contracts\Subscription\SubscriptionHandlerResolverInterface;
use AutomaNet\EventBus\Driver\Direct\DirectEventBusFactory;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionManager;
use PHPUnit\Framework\TestCase;

class DirectEventBusFactoryTest extends TestCase
{
    public function testCreateEventBus()
    {
        $handlerResolver = $this->createMock(SubscriptionHandlerResolverInterface::class);
        $subscriptionManager = new EventBusSubscriptionManager();
        $directEventBusFactory = new DirectEventBusFactory($handlerResolver);

        $eventBus = $directEventBusFactory->create([], $subscriptionManager);

        $this->assertInstanceOf(EventBusInterface::class, $eventBus);
    }
}
