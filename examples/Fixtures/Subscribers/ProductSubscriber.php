<?php

namespace AutomaNet\EventBus\Examples\Fixtures\Subscribers;

use AutomaNet\EventBus\Examples\Fixtures\Event\ProductUpdated;
use AutomaNet\EventBus\Subscription\EventSubscriber;

class ProductSubscriber extends EventSubscriber {
    public function handleProductUpdated(ProductUpdated $event)
    {
        echo $event->getName() . " has been handled by first subscriber \n";

        usleep(50000);
    }
}