<?php

namespace AutomaNet\EventBus\Tests\Unit\Events;

use AutomaNet\EventBus\Events\EventFactory;
use AutomaNet\EventBus\Examples\Fixtures\Event\ProductCreated;
use AutomaNet\EventBus\Examples\Fixtures\Event\ProductUpdated;
use AutomaNet\EventBus\Examples\Fixtures\Event\ProductUpdatedPayload;
use AutomaNet\EventBus\Message\Message;
use PHPUnit\Framework\TestCase;

class EventFactoryTest extends TestCase
{
    public function testCreateNewEvent()
    {
        $class = ProductUpdated::class;
        $payload = [
            'id' => 1,
            'name' => 'T-Shirt'
        ];

        $event = EventFactory::createNew($class, $payload);

        $this->assertInstanceOf(ProductUpdated::class, $event);
        $this->assertInstanceOf(ProductUpdatedPayload::class, $event->getPayload());
        $this->assertEquals($payload, $event->getPayload()->toArray());
    }

    public function testCreateEvent()
    {
        $class = ProductUpdated::class;
        $uuid = 'fcad9516-b768-430a-b00d-64275404f62b';
        $payload = [
            'id' => 1,
            'name' => 'T-Shirt'
        ];
        $createdAt = new \DateTimeImmutable('2022-01-01 12:13:33');

        $event = EventFactory::create($class, $uuid, $createdAt, $payload);
        $this->assertInstanceOf(ProductUpdated::class, $event);
        $this->assertInstanceOf(ProductUpdatedPayload::class, $event->getPayload());
        $this->assertEquals($uuid, $event->getUuid());
        $this->assertEquals($createdAt->format('Y-m-d H:i:s'), $event->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals($payload, $event->getPayload()->toArray());
    }

    public function testCreateFromMessage()
    {
        $uuid = 'fcad9516-b768-430a-b00d-64275404f62b';
        $payload = [
            'id' => 1,
            'name' => 'T-Shirt'
        ];
        $createdAt = new \DateTimeImmutable('2022-01-01 12:13:33');

        $message = new Message(
            $uuid,
            $payload,
            $createdAt,
            'ProductUpdated',
            'Routing.Key.Product.Updated',
            'Core'
        );

        $event = EventFactory::fromMessage(ProductUpdated::class, $message);

        $this->assertInstanceOf(ProductUpdated::class, $event);
        $this->assertInstanceOf(ProductUpdatedPayload::class, $event->getPayload());
        $this->assertEquals($uuid, $event->getUuid());
        $this->assertEquals($createdAt->format('Y-m-d H:i:s'), $event->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals($payload, $event->getPayload()->toArray());

        return $message;
    }

    /**
     * @depends testCreateFromMessage
     */
    public function testCreateFailsForWrongEventClass(Message $message)
    {
        $this->expectException(\InvalidArgumentException::class);

        EventFactory::fromMessage(ProductCreated::class, $message);
    }
}
