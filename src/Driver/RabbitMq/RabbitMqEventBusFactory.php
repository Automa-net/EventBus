<?php

namespace AutomaNet\EventBus\Driver\RabbitMq;

use AutomaNet\EventBus\Contracts\Event\EventFactoryInterface;
use AutomaNet\EventBus\Contracts\EventBus\EventBusFactoryInterface;
use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;
use AutomaNet\EventBus\Contracts\Subscription\SubscriptionHandlerResolverInterface;
use AutomaNet\EventBus\Dispatchers\EventDispatcher;
use AutomaNet\EventBus\Driver\RabbitMq\Connection\RabbitMqEventBusConnectionFactory;
use AutomaNet\EventBus\EventBus;
use AutomaNet\EventBus\Events\EventFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class RabbitMqEventBusFactory implements EventBusFactoryInterface
{
    private SubscriptionHandlerResolverInterface $subscriptionHandlerResolver;

    private EventFactoryInterface $eventFactory;

    /**
     * @param SubscriptionHandlerResolverInterface $subscriptionHandlerResolver
     * @param ?EventFactoryInterface $eventFactory
     */
    public function __construct(SubscriptionHandlerResolverInterface $subscriptionHandlerResolver, ?EventFactoryInterface $eventFactory = null)
    {
        $this->subscriptionHandlerResolver = $subscriptionHandlerResolver;
        $this->eventFactory = $eventFactory ?? new EventFactory();
    }

    /**
     * @param array $config
     * @return EventBusInterface
     * @throws \Exception
     */
    public function create(array $config, EventBusSubscriptionManagerInterface $subscriptionManager): EventBusInterface
    {
        if (!isset($config['publisher'])) {
            throw new \Exception('No publisher configuration found for this connection');
        }

        return new EventBus(
            new RabbitMqEventBusPublisher(
                $this->createConnectionFactory($config),
                $config['publisher']['exchange'],
                $this->createAMQPMessageFactory($config)
            ),
            $subscriptionManager
        );
    }

    /**
     * @param array $config
     * @param EventBusSubscriptionManagerInterface $subscriptionManager
     * @return RabbitMqEventBusConsumer
     * @throws \Exception
     */
    public function createConsumer(array $config, EventBusSubscriptionManagerInterface $subscriptionManager, ?LoggerInterface $logger = null): RabbitMqEventBusConsumer
    {
        if (!isset($config['consumer'])) {
            throw new \Exception('No consumer configuration found for this connection');
        }

        $consumerConfig = RabbitMqConsumerConfig::fromArray($config['consumer']);

        if ($logger === null) {
            $logger = new NullLogger();
        }

        return new RabbitMqEventBusConsumer(
            $this->createConnectionFactory($config),
            RabbitMqConsumerConfig::fromArray($config['consumer']),
            new EventDispatcher(
                $subscriptionManager,
                $this->subscriptionHandlerResolver,
                $this->eventFactory
            ),
            $this->createAMQPMessageFactory($config),
            $logger,
        );
    }

    /**
     * @param array $config
     * @return RabbitMqEventBusConnectionFactory
     */
    private function createConnectionFactory(array $config): RabbitMqEventBusConnectionFactory
    {
        return new RabbitMqEventBusConnectionFactory($config);
    }

    /**
     * @param array $config
     * @return MessageFactory
     */
    private function createAMQPMessageFactory(array $config): MessageFactory
    {
        return new MessageFactory($config['publisher']['routing_key_prefix'], $config['publisher']['project']);
    }
}
