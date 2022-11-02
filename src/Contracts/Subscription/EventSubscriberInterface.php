<?php

namespace AutomaNet\EventBus\Contracts\Subscription;

interface EventSubscriberInterface
{
    public static function getQueue(): ?string;
}
