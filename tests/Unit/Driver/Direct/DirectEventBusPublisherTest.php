<?php

namespace AutomaNet\EventBus\Tests\Unit\Driver\Direct;

use AutomaNet\EventBus\Dispatchers\EventDispatcher;
use AutomaNet\EventBus\Driver\Direct\DirectEventBusPublisher;
use AutomaNet\EventBus\Events\EventFactory;
use AutomaNet\EventBus\Examples\Fixtures\Event\ProductUpdated;
use AutomaNet\EventBus\Examples\Fixtures\Subscribers\ProductSubscriber;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionHandlerResolver;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class DirectEventBusPublisherTest extends TestCase
{
    public function testPublishEvent()
    {
        $containerMock = $this->createMock(ContainerInterface::class);
        $subscriberMock = $this->createMock(ProductSubscriber::class);

        $subscriberMock
            ->expects($this->once())
            ->method('handleProductUpdated');

        $containerMock->method('get')
            ->willReturn($subscriberMock);

        $subscriptionManager = new EventBusSubscriptionManager();
        $subscriptionManager->registerSubscriber(get_class($subscriberMock));

        $eventDispatcher = new EventDispatcher(
            $subscriptionManager,
            new EventBusSubscriptionHandlerResolver($containerMock),
            new EventFactory()
        );

        $publisher = new DirectEventBusPublisher($eventDispatcher);
        $publisher->publish(ProductUpdated::newFromArray([
            'id' => 1,
            'name' => 'T-shirt'
        ]));
    }
}
