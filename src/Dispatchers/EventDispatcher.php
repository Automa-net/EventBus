<?php

namespace AutomaNet\EventBus\Dispatchers;

use AutomaNet\EventBus\Contracts\Dispatcher\IEventDispatcher;
use AutomaNet\EventBus\Contracts\Dispatcher\IEventMessageDispatcher;
use AutomaNet\EventBus\Contracts\Event\EventFactoryInterface;
use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\Message\IMessage;
use AutomaNet\EventBus\Contracts\Subscription\EventBusSubscriptionManagerInterface;
use AutomaNet\EventBus\Contracts\Subscription\SubscriptionHandlerResolverInterface;

class EventDispatcher implements IEventDispatcher, IEventMessageDispatcher
{
    private EventBusSubscriptionManagerInterface $subscriptionManager;

    private SubscriptionHandlerResolverInterface $resolver;

    private EventFactoryInterface $eventFactory;

    /**
     * @param EventBusSubscriptionManagerInterface $subscriptionManager
     * @param SubscriptionHandlerResolverInterface $resolver
     * @param EventFactoryInterface $eventFactory
     */
    public function __construct(EventBusSubscriptionManagerInterface $subscriptionManager, SubscriptionHandlerResolverInterface $resolver, EventFactoryInterface $eventFactory)
    {
        $this->subscriptionManager = $subscriptionManager;
        $this->resolver = $resolver;
        $this->eventFactory = $eventFactory;
    }

    /**
     * @param EventInterface $event
     * @return void
     */
    public function dispatch(EventInterface $event): void
    {
        foreach ($this->subscriptionManager->getHandlersByName($event->getName()) as $reflectionEventHandler) {
            ($this->resolver)($reflectionEventHandler, $event);
        }
    }

    /**
     * @param IMessage $message
     * @return void
     * @throws \Exception
     */
    public function dispatchMessage(IMessage $message): void
    {
        foreach ($this->subscriptionManager->getHandlersByName($message->getEventName()) as $reflectionEventHandler) {
            ($this->resolver)($reflectionEventHandler, $this->eventFactory::fromMessage($reflectionEventHandler->getPropertyEventClass(), $message));
        }
    }
}
