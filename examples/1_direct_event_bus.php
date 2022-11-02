<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;
use AutomaNet\EventBus\Examples\Fixtures\Event\ProductUpdated;
use AutomaNet\EventBus\Examples\Fixtures\Subscribers\ProductSubscriber;
use AutomaNet\EventBus\Driver\Direct\DirectEventBusFactory;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionHandlerResolver;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionManager;

// User your existing Dependency injector container or create a new one.
$containerBuilder = new ContainerBuilder();
$containerBuilder->autowire(ProductSubscriber::class, ProductSubscriber::class);

// Create a new instance of the eventbus factory
$eventBusFactory = new DirectEventBusFactory(
    new EventBusSubscriptionHandlerResolver($containerBuilder),
);

// Create a new instance of the subscription manager
$subscriptionManager = new EventBusSubscriptionManager();

// Create a new instance of the eventbus
$eventBus = $eventBusFactory->create([], $subscriptionManager);

// Register subscriber
$eventBus->subscribe(ProductSubscriber::class);

// Publish events
$event = ProductUpdated::newFromArray([
    'id' => 1,
    'name' => 'Hello'
]);

$eventBus->publish([$event]);
