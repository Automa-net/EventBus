<?php

namespace AutomaNet\EventBus\Contracts;

interface IEventConsumer
{
    public function consume(): void;
}
