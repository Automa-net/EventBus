<?php

namespace AutomaNet\EventBus\Contracts\Dispatcher;

use AutomaNet\EventBus\Contracts\Message\IMessage;

interface IEventMessageDispatcher
{
    public function dispatchMessage(IMessage $message): void;
}
