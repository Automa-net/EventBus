<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;
use AutomaNet\EventBus\Examples\Fixtures\Subscribers\ProductSubscriber;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use AutomaNet\EventBus\Driver\RabbitMq\RabbitMqEventBusFactory;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionHandlerResolver;
use AutomaNet\EventBus\Subscription\EventBusSubscriptionManager;

$config = [
    'host' => INTEGRATION_EVENTBUS_AMQP_HOST,
    'port' => INTEGRATION_EVENTBUS_AMQP_PORT,
    'user' => INTEGRATION_EVENTBUS_AMQP_USER,
    'password' => INTEGRATION_EVENTBUS_AMQP_PASSWORD,
    'vhost' => INTEGRATION_EVENTBUS_AMQP_VHOST,
    'options' => [],

    'consumer' => [
        'queue' => INTEGRATION_EVENTBUS_AMQP_CONSUMER_QUEUE,
        'enable_heartbeat_sender' => true,
    ],

    'publisher' => [
        'project' => INTEGRATION_EVENTBUS_PROJECT_NAME,
        'exchange' => INTEGRATION_EVENTBUS_AMQP_PUBLISHER_EXCHANGE,
        'routing_key_prefix' => INTEGRATION_EVENTBUS_AMQP_PUBLISHER_ROUTING_KEY_PREFIX,
    ],
];

// For test purposes, Create a new exchange and queue
$connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
$channel = $connection->channel();
$channel->queue_declare($config['consumer']['queue'], false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable([
    'x-dead-letter-exchange' => 'vendor.example.exchange.dl'
]));
$channel->exchange_declare($config['publisher']['exchange'], AMQPExchangeType::TOPIC, false, true, false);
$channel->queue_bind($config['consumer']['queue'], $config['publisher']['exchange'], 'vendor.#');
$channel->close();
$connection->close();

// User your existing Dependency injector container or create a new one.
$containerBuilder = new ContainerBuilder();
$containerBuilder->autowire(ProductSubscriber::class, ProductSubscriber::class);

// Create a new instance of the eventbus factory
$eventBusFactory = new RabbitMqEventBusFactory(
    new EventBusSubscriptionHandlerResolver($containerBuilder)
);

// Create a new instance of the subscription manager
$subscriptionManager = new EventBusSubscriptionManager();
$subscriptionManager->registerSubscriber(ProductSubscriber::class);

// Create a new instance of the RabbitMqConsumer
$consumer = $eventBusFactory->createConsumer($config, $subscriptionManager, new \Symfony\Component\Console\Logger\ConsoleLogger(new \Symfony\Component\Console\Output\ConsoleOutput()));

// Consume events
$consumer->consume();

