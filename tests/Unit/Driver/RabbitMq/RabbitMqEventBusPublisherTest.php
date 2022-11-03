<?php

namespace AutomaNet\EventBus\Tests\Unit\Driver\RabbitMq;

use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqEventBusConnectionFactory;
use AutomaNet\EventBus\Driver\RabbitMq\RabbitMqEventBusPublisher;
use AutomaNet\EventBus\Examples\Fixtures\Event\ProductUpdated;
use AutomaNet\EventBus\Exceptions\MaxPublishAttemptsException;
use AutomaNet\EventBus\Message\MessageFactory;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class RabbitMqEventBusPublisherTest extends TestCase
{
    protected function prepareAMQPChannel()
    {
        return $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testPublishEvents()
    {
        $rabbitMqConnectionFactoryMock = $this->createMock(RabbitMqEventBusConnectionFactory::class);

        $channel = $this->prepareAMQPChannel();

        $rabbitMqConnectionFactoryMock->expects($this->once())
            ->method('connect')
            ->willReturn($channel);

        $channel->expects($this->once())
            ->method('basic_publish')
            ->with($this->callback(function(AMQPMessage $amqpMessage) {
                $this->assertSame('{"id":2,"name":"T-shirt"}', $amqpMessage->getBody());

                return true;
            }), 'exchange', 'Prefix.Product.Updated');

        $eventBusPublisher = new RabbitMqEventBusPublisher(
            $rabbitMqConnectionFactoryMock,
            'exchange',
            new MessageFactory('Prefix', 'Project.A')
        );

        $eventBusPublisher->publish(ProductUpdated::newFromArray([
            'id' => 2,
            'name' => 'T-shirt'
        ]));
    }

    public function testFailMaxPublishAttemptsExceptionPublish()
    {
        $this->expectException(MaxPublishAttemptsException::class);

        $rabbitMqConnectionFactoryMock = $this->createMock(RabbitMqEventBusConnectionFactory::class);

        $channel = $this->prepareAMQPChannel();

        $rabbitMqConnectionFactoryMock->expects($this->exactly(3))
            ->method('connect')
            ->willReturn($channel);

        $channel
            ->expects($this->exactly(3))
            ->method('basic_publish')
            ->willThrowException(new AMQPConnectionClosedException());

        $eventBusPublisher = new RabbitMqEventBusPublisher(
            $rabbitMqConnectionFactoryMock,
            'exchange',
            new MessageFactory('Prefix', 'Project.A'),
            0,
            3
        );

        $eventBusPublisher->publish(ProductUpdated::newFromArray([
            'id' => 2,
            'name' => 'T-shirt'
        ]));
    }
}
