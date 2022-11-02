<?php

namespace AutomaNet\EventBus\Tests\Unit\Subscription;

use AutomaNet\EventBus\Examples\Fixtures\Event\ProductUpdated;
use AutomaNet\EventBus\Examples\Fixtures\Subscribers\ProductSubscriber;
use AutomaNet\EventBus\Reflection\ReflectionEventHandler;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionHandlerResolver;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class EventBusSubscriptionHandlerResolverTest extends TestCase
{
    public function testInvokeHandler()
    {
        $containerMock = $this->createMock(ContainerInterface::class);
        $subscriberMock = $this->createMock(ProductSubscriber::class);

        $subscriberMock
            ->expects($this->once())
            ->method('handleProductUpdated');

        $containerMock->method('get')
            ->willReturn($subscriberMock);

        $resolver = new EventBusSubscriptionHandlerResolver($containerMock);

        $reflectionEventHandler = new ReflectionEventHandler(new \ReflectionMethod($subscriberMock, 'handleProductUpdated'));

        $resolver->__invoke($reflectionEventHandler, ProductUpdated::newFromArray(['id' => 1, 'name' => 'T-Shirt']));
    }
}
