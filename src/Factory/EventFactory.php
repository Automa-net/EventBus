<?php

namespace AutomaNet\EventBus\Factory;

use AutomaNet\EventBus\Contracts\Event\EventFactoryInterface;
use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\Event\EventPayloadInterface;
use AutomaNet\EventBus\Contracts\Message\IMessage;
use Ramsey\Uuid\Uuid;

class EventFactory implements EventFactoryInterface
{
    /**
     * @template T of EventInterface
     * @param class-string<T> $eventClass
     * @param array $payload
     * @return T
     * @throws \Exception
     */
    public static function createNew(string $eventClass, array $payload): EventInterface
    {
        return self::create($eventClass, Uuid::uuid4()->toString(), new \DateTimeImmutable('now'), $payload);
    }

    /**
     * @template T of EventInterface
     * @param class-string<T> $eventClass
     * @param string $uuid
     * @param \DateTimeImmutable $createdAt
     * @param array $payload
     * @return T
     * @throws \ReflectionException
     */
    public static function create(string $eventClass, string $uuid, \DateTimeImmutable $createdAt, array $payload): EventInterface
    {
        $reflection = new \ReflectionClass($eventClass);

        if (!$reflection->isSubclassOf(EventInterface::class)) {
            throw new \Exception('Event class is not part of EventInterface');
        }

        return $reflection->newInstance($uuid, $createdAt, self::createPayload($reflection, $payload));
    }

    /**
     * @template T of EventInterface
     * @param class-string<T> $eventClass
     * @param IMessage $message
     * @return T
     * @throws \ReflectionException
     */
    public static function fromMessage(string $eventClass, IMessage $message): EventInterface
    {
        return self::create($eventClass, $message->getUuid(), $message->getCreatedAt(), $message->getBody());
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param array $payload
     * @return EventPayloadInterface
     * @throws \ReflectionException
     */
    private static function createPayload(\ReflectionClass $reflectionClass, array $payload): EventPayloadInterface
    {
        $methodReflection = $reflectionClass->getMethod('getPayload');
        $payloadReflectionReturnType = $methodReflection->getReturnType();

        if (!$payloadReflectionReturnType instanceof \ReflectionNamedType) {
            throw new \Exception('Missing payload type');
        }

        $payloadClassName = $payloadReflectionReturnType->getName();

        if (!is_subclass_of($payloadClassName, EventPayloadInterface::class)) {
            throw new \Exception('Event Payload class is not part of EventPayloadInterface');
        }

        return new $payloadClassName($payload);
    }
}
