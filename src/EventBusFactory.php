<?php

namespace AutomaNet\EventBus;

use AutomaNet\EventBus\Contracts\EventBus\EventBusFactoryInterface;
use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;
use AutomaNet\EventBus\Driver\Direct\DirectEventBusFactory;
use AutomaNet\EventBus\Driver\RabbitMq\RabbitMqConsumerConfig;

/**
 * @phpstan-import-type DirectEventBusFactoryConfig from DirectEventBusFactory
 * @phpstan-import-type RabbitMqConsumerConfigArray from RabbitMqConsumerConfig
 */
class EventBusFactory
{
    /**
     * @var EventBusFactoryInterface[]
     */
    private array $factories;

    /**
     * @var array<string, DirectEventBusFactoryConfig|RabbitMqConsumerConfigArray>
     */
    private array $connectionConfig;

    /**
     * @param EventBusFactoryInterface[] $factories
     * @param array<string, DirectEventBusFactoryConfig|RabbitMqConsumerConfigArray> $connectionConfig
     */
    public function __construct(array $factories, array $connectionConfig)
    {
        $this->factories = $factories;
        $this->connectionConfig = $connectionConfig;
    }

    /**
     * @param string $connection
     * @param EventBusSubscriptionManagerInterface $subscriptionManager
     * @return EventBusInterface
     * @throws \Exception
     */
    public function create(string $connection, EventBusSubscriptionManagerInterface $subscriptionManager): EventBusInterface
    {
        $driver = $this->getConnectionDriver($connection);

        if (!isset($this->factories[$driver])) {
            throw new \Exception('Factory for driver ' . $driver . ' not found');
        }

        return $this->factories[$driver]->create($this->getConnectionConfig($connection), $subscriptionManager);
    }

    /**
     * Retrieve driver for connection
     *
     * @param string $connection
     * @return string
     * @throws \Exception
     */
    private function getConnectionDriver(string $connection): string
    {
        return $this->getConnectionConfig($connection)['driver'];
    }

    /**
     * Retrieve config for connection
     *
     * @param string $connection
     * @return DirectEventBusFactoryConfig|RabbitMqConsumerConfigArray
     */
    private function getConnectionConfig(string $connection): array
    {
        return $this->connectionConfig[$connection];
    }
}
