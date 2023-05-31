<?php

namespace AutomaNet\EventBus\Tests\Unit\Driver\RabbitMq;

use AutomaNet\EventBus\Dispatchers\EventDispatcher;
use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqEventBusConnectionFactory;
use AutomaNet\EventBus\Driver\RabbitMq\RabbitMqConsumerConfig;
use AutomaNet\EventBus\Driver\RabbitMq\RabbitMqEventBusConsumer;
use AutomaNet\EventBus\Events\EventFactory;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionHandlerResolver;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionManager;
use AutomaNet\EventBus\Driver\RabbitMq\MessageFactory;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RabbitMqEventBusConsumerTest extends TestCase
{
    protected function prepareAMQPConnection()
    {
        $connection = $this->getMockBuilder(AMQPStreamConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection->method('isConnected')
            ->willReturn(true);

        return $connection;
    }

    protected function prepareAMQPChannel()
    {
        $channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $channel->method('getConnection')
            ->willReturn($this->prepareAMQPConnection());

        $channel->method('is_consuming')
            ->willReturn(true);

        $channel->method('is_open')
            ->willReturn(true);

        return $channel;
    }

    public function testConsume()
    {
        $rabbitMqConnectionFactoryMock = $this->createMock(RabbitMqEventBusConnectionFactory::class);

        $channel = $this->prepareAMQPChannel();

        $rabbitMqConnectionFactoryMock->expects($this->once())
            ->method('connect')
            ->willReturn($channel);

        $containerMock = $this->createMock(ContainerInterface::class);

        $subscriptionManager = new EventBusSubscriptionManager();

        $eventDispatcher = new EventDispatcher(
            $subscriptionManager,
            new EventBusSubscriptionHandlerResolver($containerMock),
            new EventFactory()
        );

        $channel
            ->expects($this->once())
            ->method('basic_consume')
            ->withAnyParameters()
            ->willReturn(true);

        $channel
            ->expects($this->once())
            ->method('consume');

        $consumer = new RabbitMqEventBusConsumer(
            $rabbitMqConnectionFactoryMock,
            RabbitMqConsumerConfig::fromArray([
                'queue' => 'queue'
            ]),
            $eventDispatcher,
            new MessageFactory('Prefix', 'Project.A'),
        );

        $consumer->consume();
    }
}
