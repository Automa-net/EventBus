<?php

namespace AutomaNet\EventBus\Driver\RabbitMq\Connection;

use PhpAmqpLib\Channel\AMQPChannel;

trait RabbitMqFactoryConnectable
{
    private AMQPChannel $channel;

    private RabbitMqEventBusConnectionFactory $connectionFactory;

    /**
     * @return AMQPChannel
     */
    private function getChannel(): AMQPChannel
    {
        if (!$this->isConnectionOpen()) {
            $this->channel = $this->createChannel();
        }

        if (in_array(RabbitMqHasHeartbeatSender::class, class_uses($this))) {
            $this->registerHeartbeatSender($this->channel); /** @phpstan-ignore-line */
        }

        return $this->channel;
    }

    public function isConnectionOpen(): bool
    {
        if (!isset($this->channel) ||
            $this->channel->getConnection() === null ||
            !$this->channel->getConnection()->isConnected() ||
            !$this->channel->is_open()) {
            return false;
        }

        return true;
    }

    /**
     * @return AMQPChannel
     */
    private function createChannel(): AMQPChannel
    {
        return $this->connectionFactory->connect();
    }

    public function __destruct()
    {
        $this->closeConnection();
    }

    public function closeConnection(): void
    {
        if (in_array(RabbitMqHasHeartbeatSender::class, class_uses($this))) {
            $this->unregisterHeartbeatSender(); /** @phpstan-ignore-line */
        }

        if ($this->isConnectionOpen()) {
            $this->channel->getConnection()->close();
            $this->channel->close();
        }
    }
}
