<?php

namespace AutomaNet\EventBus\Driver\RabbitMq\Connection;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnectionConfig;
use PhpAmqpLib\Connection\AMQPConnectionFactory;

class RabbitMqEventBusConnectionFactory
{
    private array $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return AMQPChannel
     */
    public function createChannel(): AMQPChannel
    {
        $AMQPConnectionConfig = new AMQPConnectionConfig();
        $AMQPConnectionConfig->setHost($this->config['host']);
        $AMQPConnectionConfig->setPort($this->config['port']);
        $AMQPConnectionConfig->setUser($this->config['user']);
        $AMQPConnectionConfig->setPassword($this->config['password']);
        $AMQPConnectionConfig->setVhost($this->config['vhost']);
        $AMQPConnectionConfig->setHeartbeat(60);

        $connection = AMQPConnectionFactory::create($AMQPConnectionConfig);

        $connection->set_close_on_destruct(true);

        return $connection->channel();
    }

    public function connect(): AMQPChannel
    {
        return $this->createChannel();
    }
}
