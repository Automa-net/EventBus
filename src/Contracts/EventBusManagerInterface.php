<?php

namespace AutomaNet\EventBus\Contracts;

use AutomaNet\EventBus\Contracts\EventBus\EventBusInterface;

interface EventBusManagerInterface extends EventBusInterface
{
    public function subscribe($eventListenerClassName, ?int $priority = 100, ?string $connection = null): void;

    public function publish(array $events, ?string $connection = null): void;

    public function getEventBus(?string $connection = null): EventBusInterface;
}
