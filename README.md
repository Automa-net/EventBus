#  Automa.net - EventBus

<div align="center"></div>

[![Latest Stable Version](http://poser.pugx.org/automa.net/eventbus/v)](https://packagist.org/packages/automa.net/eventbus)
[![Latest Unstable Version](http://poser.pugx.org/automa.net/eventbus/v/unstable)](https://packagist.org/packages/automa.net/eventbus) 
[![License](http://poser.pugx.org/automa.net/eventbus/license)](https://packagist.org/packages/automa.net/eventbus) 
[![PHP Version Require](http://poser.pugx.org/automa.net/eventbus/require/php)](https://packagist.org/packages/automa.net/eventbus)

EventBus is a publish/subscribe event bus library.

The library gives the possibility to use multiple events buses at once, to do that use ``EventBusManager``.
Then you can register separately Domain EventBus and Integration EventBus.

* Required dependency injection container!

## Drivers:

- Direct - Instant resolve and dispatch event on handlers.
- RabbitMQ - Publish events on queue and execute them separately using consumers.

### Example of RabbitMq driver config:

```php
$config = [
    'host' => INTEGRATION_EVENTBUS_AMQP_HOST,
    'port' => INTEGRATION_EVENTBUS_AMQP_PORT,
    'user' => INTEGRATION_EVENTBUS_AMQP_USER,
    'password' => INTEGRATION_EVENTBUS_AMQP_PASSWORD,
    'vhost' => INTEGRATION_EVENTBUS_AMQP_VHOST,
    'options' => [],

    'consumer' => [
        'queue' => INTEGRATION_EVENTBUS_AMQP_CONSUMER_QUEUE,
        'enable_heartbeat_sender' => false
    ],

    'publisher' => [
        'project' => INTEGRATION_EVENTBUS_PROJECT_NAME,
        'exchange' => INTEGRATION_EVENTBUS_AMQP_PUBLISHER_EXCHANGE,
        'routing_key_prefix' => INTEGRATION_EVENTBUS_AMQP_PUBLISHER_ROUTING_KEY_PREFIX,
    ],
];
```