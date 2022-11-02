<?php

namespace AutomaNet\EventBus\Factory;

use AutomaNet\EventBus\Contracts\EventBus\EventBusFactoryInterface;
use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;

class EventBusFactory
{
    /**
     * @var EventBusFactoryInterface[]
     */
    private array $factories;

    private array $connections;

    /**
     * @param EventBusFactoryInterface[] $factories
     */
    public function __construct(array $factories, array $connections)
    {
        $this->factories = $factories;
        $this->connections = $connections;
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
        $driver = $this->getConnectionConfig($connection)['driver'];

        if (!$driver) {
            throw new \Exception('Missing driver');
        }

        return $driver;
    }

    /**
     * Retrieve config for connection
     *
     * @param string $connection
     * @return array
     */
    private function getConnectionConfig(string $connection): array
    {
        return $this->connections[$connection];
    }
}
