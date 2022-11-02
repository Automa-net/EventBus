<?php

namespace AutomaNet\EventBus\Subscription;

use AutomaNet\EventBus\Contracts\Subscription\EventSubscriberInterface;

abstract class EventSubscriber implements EventSubscriberInterface
{
    protected static ?string $connection = null;

    public static function getQueue(): ?string
    {
        return static::$connection;
    }
}
