<?php

namespace AutomaNet\EventBus\Tests\Unit\Message;

use AutomaNet\EventBus\Contracts\Message\IMessage;
use AutomaNet\EventBus\Examples\Fixtures\Event\ProductUpdated;
use AutomaNet\EventBus\Message\MessageFactory;
use PHPUnit\Framework\TestCase;

class MessageFactoryTest extends TestCase
{
    public function testCreateMessage()
    {
        $uuid = 'fcad9516-b768-430a-b00d-64275404f62b';
        $eventName = 'ProductUpdated';
        $projectName = 'automa.net';
        $createdAt = new \DateTimeImmutable('2022-01-01 12:13:33');
        $payload = [
            'id' => 1,
            'name' => 'T-Shirt'
        ];
        $prefix = 'project.prefix';
        $routingKey = 'Routing.Key.Product.Updated';

        $messageFactory = new MessageFactory($prefix, $projectName);

        $message = $messageFactory->create(
            $uuid,
            $eventName,
            $createdAt,
            $payload,
            $projectName,
            $routingKey
        );

        $this->assertInstanceOf( IMessage::class, $message);
        $this->assertSame($uuid, $message->getUuid());
        $this->assertSame($eventName, $message->getEventName());
        $this->assertSame($createdAt->format('Y-m-d H:i:s'), $message->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals($payload, $message->getBody());
        $this->assertSame($uuid, $message->getHeaders()[IMessage::HEADER_UUID_KEY]);
        $this->assertSame($eventName, $message->getHeaders()[IMessage::HEADER_EVENT_NAME_KEY]);
        $this->assertSame($createdAt->format(\DateTimeInterface::ATOM), $message->getHeaders()[IMessage::HEADER_CREATED_AT]);
        $this->assertSame($projectName, $message->getHeaders()[IMessage::HEADER_PUBLISHED_BY]);
        $this->assertSame($routingKey, $message->getRoutingKey());
    }

    public function testCreateMessageFromEvent()
    {
        $projectName = 'automa.net';
        $prefix = 'project.prefix';
        $payload = [
            'id' => 1,
            'name' => 'T-Shirt'
        ];

        $event = ProductUpdated::newFromArray($payload);

        $messageFactory = new MessageFactory($prefix, $projectName);
        $message = $messageFactory->fromEvent($event);

        $this->assertInstanceOf( IMessage::class, $message);
        $this->assertSame($event->getUuid(), $message->getUuid());
        $this->assertSame($event->getName(), $message->getEventName());
        $this->assertSame($event->getCreatedAt()->format('Y-m-d H:i:s'), $message->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals($event->getPayload()->toArray(), $message->getBody());
        $this->assertSame($projectName, $message->getHeaders()[IMessage::HEADER_PUBLISHED_BY]);
        $this->assertSame($prefix . '.Product.Updated', $message->getRoutingKey());
    }
}
