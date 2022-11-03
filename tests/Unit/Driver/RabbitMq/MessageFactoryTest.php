<?php

namespace AutomaNet\EventBus\Tests\Unit\Driver\RabbitMq;

use AutomaNet\EventBus\Driver\RabbitMq\MessageFactory;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class MessageFactoryTest extends TestCase
{
    public function testCreateMessageFromAMQPMessage()
    {
        $createdAt = new \DateTimeImmutable('2022-01-01 12:13:33');
        $publishedAt = new \DateTimeImmutable('2022-01-01 12:14:33');
        $uuid = 'fcad9516-b768-430a-b00d-64275404f62b';
        $eventName = 'ProductUpdated';
        $publishedBy = 'Project.A';
        $payload = [
            'id' => 1,
            'name' => 'T-Shirt'
        ];
        $routingKey = 'routing_key';

        $message = new AMQPMessage(json_encode($payload), [
            'application_headers' => [
                'uuid' => $uuid,
                'event_name' => $eventName,
                'created_at' => $createdAt->format(\DateTimeInterface::ATOM),
                'published_by' => $publishedBy,
                'published_at' => $publishedAt->format(\DateTimeInterface::ATOM)
            ]
        ]);
        $message->setDeliveryInfo(1, false, 'exchange', $routingKey);

        $messageFactory = new MessageFactory('prefix', 'project');

        $message = $messageFactory->fromAMQPMessage($message);

        $this->assertSame($uuid, $message->getUuid());
        $this->assertSame($createdAt->format(\DateTimeInterface::ATOM), $message->getCreatedAt()->format(\DateTimeInterface::ATOM));
        $this->assertEquals($payload, $message->getBody());
        $this->assertEquals($eventName, $message->getEventName());
        $this->assertSame($publishedAt->format(\DateTimeInterface::ATOM), $message->getPublishedAt()->format(\DateTimeInterface::ATOM));
        $this->assertSame($publishedBy, $message->getPublishedBy());
        $this->assertSame($routingKey, $message->getRoutingKey());
    }
}
