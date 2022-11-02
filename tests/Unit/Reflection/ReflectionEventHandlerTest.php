<?php

namespace AutomaNet\EventBus\Tests\Unit\Reflection;

use AutomaNet\EventBus\Examples\Fixtures\Event\ProductUpdated;
use AutomaNet\EventBus\Examples\Fixtures\Subscribers\ProductSubscriber;
use AutomaNet\EventBus\Reflection\ReflectionEventHandler;
use PHPUnit\Framework\TestCase;

class ReflectionEventHandlerTest extends TestCase
{
    public function testReflectionHandler()
    {
        $productSubscriber = new ProductSubscriber();

        $reflectionHandler = new ReflectionEventHandler(new \ReflectionMethod($productSubscriber, 'handleProductUpdated'));

        $this->assertSame(ProductSubscriber::class, $reflectionHandler->getSubscriberClass());
        $this->assertSame('handleProductUpdated', $reflectionHandler->getMethodName());
        $this->assertSame('ProductUpdated', $reflectionHandler->getEventName());
        $this->assertSame(ProductUpdated::class, $reflectionHandler->getPropertyEventClass());
        $this->assertIsCallable($reflectionHandler->getClosure($productSubscriber));

    }
}
