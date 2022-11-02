<?php

namespace AutomaNet\EventBus\Contracts\Event;

use AutomaNet\EventBus\Contracts\Message\IMessage;

interface EventFactoryInterface
{
    /**
     * @template T of EventInterface
     * @param class-string<T> $eventClass
     * @param array $payload
     * @return T
     * @throws \Exception
     */
    public static function createNew(string $eventClass, array $payload): EventInterface;

    /**
     * @template T of EventInterface
     * @param class-string<T> $eventClass
     * @param string $uuid
     * @param \DateTimeImmutable $createdAt
     * @param array $payload
     * @return T
     * @throws \ReflectionException
     */
    public static function create(string $eventClass, string $uuid, \DateTimeImmutable $createdAt, array $payload): EventInterface;

    /**
     * @template T of EventInterface
     * @param class-string<T> $eventClass
     * @param IMessage $message
     * @return T
     * @throws \ReflectionException
     */
    public static function fromMessage(string $eventClass, IMessage $message): EventInterface;
}
