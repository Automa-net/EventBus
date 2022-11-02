<?php

namespace AutomaNet\EventBus\Tests\Unit\Subscription;

use AutomaNet\EventBus\Examples\Fixtures\Subscribers\ProductSubscriber;
use AutomaNet\EventBus\Examples\Fixtures\Subscribers\SecondProductSubscriber;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionManager;
use PHPUnit\Framework\TestCase;

class EventBusSubscriptionManagerTest extends TestCase
{
    public function testRegisterSubscriber()
    {
        $subscriptionManager = new EventBusSubscriptionManager();

        $subscriptionManager->registerSubscriber(ProductSubscriber::class, 200);
        $subscriptionManager->registerSubscriber(SecondProductSubscriber::class, 10);

        $this->assertCount(2, $subscriptionManager->getHandlersByName('ProductUpdated'));
        $this->assertSame(SecondProductSubscriber::class, $subscriptionManager->getHandlersByName('ProductUpdated')[0]->getSubscriberClass());
        $this->assertSame(ProductSubscriber::class, $subscriptionManager->getHandlersByName('ProductUpdated')[1]->getSubscriberClass());
    }

    public function testFailDoubleRegisterSameSubscriber()
    {
        $this->expectException(\InvalidArgumentException::class);

        $subscriptionManager = new EventBusSubscriptionManager();

        $subscriptionManager->registerSubscriber(ProductSubscriber::class);
        $subscriptionManager->registerSubscriber(ProductSubscriber::class);
    }
}
