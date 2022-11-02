<?php

namespace AutomaNet\EventBus\Driver\RabbitMq\Connection;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\Heartbeat\PCNTLHeartbeatSender;

trait RabbitMqHasHeartbeatSender
{
    private PCNTLHeartbeatSender $heartbeatSender;

    public function registerHeartbeatSender(AMQPChannel $channel)
    {
        if (in_array(RabbitMqFactoryConnectable::class, class_uses($this))) {
            $this->heartbeatSender = new PCNTLHeartbeatSender($channel->getConnection());
            $this->heartbeatSender->register();
        }
    }

    public function unregisterHeartbeatSender(): void
    {
        if (isset($this->heartbeatSender)) {
            $this->heartbeatSender->unregister();
        }
    }
}
