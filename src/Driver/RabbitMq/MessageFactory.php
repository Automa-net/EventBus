<?php

namespace AutomaNet\EventBus\Driver\RabbitMq;

use AutomaNet\EventBus\Contracts\Message\IMessage;
use AutomaNet\EventBus\Driver\RabbitMq\Contracts\MessageFactoryInterface;
use PhpAmqpLib\Message\AMQPMessage;

class MessageFactory extends \AutomaNet\EventBus\Message\MessageFactory implements MessageFactoryInterface
{
    /**
     * @param AMQPMessage $message
     * @return IMessage
     * @throws \Exception
     */
    public function fromAMQPMessage(AMQPMessage $message): IMessage
    {
        return $this->create(
            $message->get('application_headers')[IMessage::HEADER_UUID_KEY],
            $message->get('application_headers')[IMessage::HEADER_EVENT_NAME_KEY],
            new \DateTimeImmutable($message->get('application_headers')[IMessage::HEADER_CREATED_AT]),
            json_decode($message->getBody(), true),
            $message->get('application_headers')[IMessage::HEADER_PUBLISHED_BY],
            $message->getRoutingKey(),
            new \DateTimeImmutable($message->get('application_headers')[IMessage::HEADER_PUBLISHED_AT]),
        );
    }
}
