<?php

namespace AutomaNet\EventBus\Contracts\Dispatcher;

use AutomaNet\EventBus\Contracts\Event\EventInterface;

interface IEventDispatcher
{
    public function dispatch(EventInterface $event): void;
}
