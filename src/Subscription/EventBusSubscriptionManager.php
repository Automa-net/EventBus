<?php

namespace AutomaNet\EventBus\Subscription;

use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;
use AutomaNet\EventBus\Reflection\ReflectionEventHandler;
use AutomaNet\EventBus\Reflection\ReflectionEventSubscriber;

class EventBusSubscriptionManager implements EventBusSubscriptionManagerInterface
{
    /**
     * @var array<string, array<int, array<ReflectionEventHandler>>>
     */
    private array $handlers = [];

    /**
     * @var array<string>
     */
    private array $registeredListeners = [];

    /**
     * @param string $subscriber
     * @param int $priority
     * @return void
     * @throws \Exception
     */
    public function registerSubscriber(string $subscriber, int $priority = 100)
    {
        $this->assertListenerIsNotRegistered($subscriber);

        foreach ((new ReflectionEventSubscriber($subscriber))->getHandlers() as $reflectionEventHandler) {
            $this->registerHandler($reflectionEventHandler->getEventName(), $reflectionEventHandler, $priority);
        }

        $this->registeredListeners[] = $subscriber;
    }

    /**
     * @param string $subscriber
     * @return void
     */
    private function assertListenerIsNotRegistered(string $subscriber)
    {
        if (in_array($subscriber, $this->registeredListeners)) {
            throw new \InvalidArgumentException('Listener ' . $subscriber . ' already registered');
        }
    }

    /**
     * @param string $eventName
     * @param ReflectionEventHandler $reflectionEventHandler
     * @param int $priority
     * @return void
     */
    public function registerHandler(string $eventName, ReflectionEventHandler $reflectionEventHandler, int $priority = 100)
    {
        if (!isset($this->handlers[$eventName])) {
            $this->handlers[$eventName] = [];
        }

        if (!isset($this->handlers[$eventName][$priority])) {
            $this->handlers[$eventName][$priority] = [];
            ksort($this->handlers[$eventName]);
        }

        $this->handlers[$eventName][$priority][] = $reflectionEventHandler;
    }

    /**
     * @param string $eventName
     * @return ReflectionEventHandler[]
     */
    public function getHandlersByName(string $eventName): array
    {
        if (!isset($this->handlers[$eventName])) {
            return [];
        }

        return array_reduce($this->handlers[$eventName], function (array $carry, array $item) {
            array_push($carry, ...$item);

            return $carry;
        }, []);
    }
}
