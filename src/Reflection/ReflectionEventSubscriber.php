<?php

namespace AutomaNet\EventBus\Reflection;

use AutomaNet\EventBus\Contracts\Subscription\EventSubscriberInterface;

class ReflectionEventSubscriber
{
    /**
     * @var \ReflectionClass<EventSubscriberInterface>
     */
    private \ReflectionClass $reflectionClass;

    /**
     * @param string $eventListener
     * @throws \ReflectionException
     */
    public function __construct(string $eventListener)
    {
        $this->reflectionClass = new \ReflectionClass($eventListener);
    }

    /**
     * @return ReflectionEventHandler[]
     * @throws \Exception
     */
    public function getHandlers(): array
    {
        $handlers = array_map(fn (\ReflectionMethod $reflectionMethod) => new ReflectionEventHandler($reflectionMethod), array_filter(
            $this->reflectionClass->getMethods(),
            fn (\ReflectionMethod $reflectionMethod) => $reflectionMethod->getName() !== ReflectionEventHandler::HANDLE_METHOD_PREFIX && strpos($reflectionMethod->getName(), ReflectionEventHandler::HANDLE_METHOD_PREFIX) === 0
        ));

        if (empty($handlers)) {
            throw new \Exception('No handlers found');
        }

        return $handlers;
    }
}
