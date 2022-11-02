<?php

namespace AutomaNet\EventBus\Driver\RabbitMq\Contracts;

use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\Message\IMessage;
use PhpAmqpLib\Message\AMQPMessage;

interface MessageFactoryInterface
{
    public function fromEvent(EventInterface $event): IMessage;

    public function fromAMQPMessage(AMQPMessage $message): IMessage;
}
