<?php

namespace AutomaNet\EventBus\Tests\Unit\Reflection;

use AutomaNet\EventBus\Examples\Fixtures\Subscribers\EmptySubscriber;
use AutomaNet\EventBus\Examples\Fixtures\Subscribers\ProductSubscriber;
use AutomaNet\EventBus\Reflection\ReflectionEventSubscriber;
use PHPUnit\Framework\TestCase;

class ReflectionEventSubscriberTest extends TestCase
{
    public function testReflectSubscriber()
    {
        $reflection = new ReflectionEventSubscriber(ProductSubscriber::class);

        $this->assertCount(1, $reflection->getHandlers());
    }

    public function testFailNoHandlersInSubscriber()
    {
        $this->expectException(\Exception::class);

        $reflections = new ReflectionEventSubscriber(EmptySubscriber::class);

        $reflections->getHandlers();
    }
}
