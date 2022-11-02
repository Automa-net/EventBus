<?php

namespace AutomaNet\EventBus\Driver\Direct;

use AutomaNet\EventBus\Contracts\Dispatcher\IEventDispatcher;
use AutomaNet\EventBus\Contracts\Event\EventInterface;
use AutomaNet\EventBus\Contracts\IEventPublisher;

class DirectEventBusPublisher implements IEventPublisher
{
    private IEventDispatcher $dispatcher;

    /**
     * @param IEventDispatcher $dispatcher
     */
    public function __construct(IEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Publish and event
     *
     * @param EventInterface $event
     * @return void
     */
    public function publish(EventInterface $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
