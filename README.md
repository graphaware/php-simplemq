### GraphAware's PHP Simple Message Queue for RabbitMQ

RabbitMQ's Rapid Application Development.

This library makes possible to create channels, queues, bindings, producers, consumers, .. on the fly by only providing 
a simple YAML configuration file.

[![Build Status](https://travis-ci.org/graphaware/php-simplemq.svg?branch=master)](https://travis-ci.org/graphaware/php-simplemq)

### Usage

Require the library dependency :

```bash
composer require graphaware/php-simplemq
```

Define the connections, exchanges, producers and consumers, eg:

```yaml
simple_mq:
  connections:
    default:
      host: 192.168.59.103
      port: 5672
      user: admin
      password: error
      vhost: "/"

  exchanges:
    logs:
      connection: default
      type: fanout
      durable: true

    error-logs:
      connection: default
      type: direct
      durable: true

  producers:
    logs:
      exchange: logs

    errors:
      exchange: error-logs
      routing_key: error

  consumers:
    logs-printer:
      exchange: logs
      ack: true
      queue:
        name: my-app-all-logs
        durable: true
        qos:
          prefetch_count: 1

    error-logs-recorder:
      exchange: error-logs
      queue:
        name: my-app-error-logs
        durable: true
        qos:
          prefetch_size: 1
      bindings:
        -
          queue: my-app-error-logs
          routing_key: error
```

Bootstrap the library by providing your configuration file location :

```php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\SimpleMQ\SimpleMQ;

$smq = SimpleMQ::withYAMLConfigFile(__DIR__.'/path_to_your_config_file.yml');
```

Based on the example configuration, producers named `logs` and `errors` as well as consumers named `logs-printer` and 
`error-logs-recorder` are available through the library.

To retrieve and start consuming queues, you can get the consumer with the following method :

```php
$consumer = $smq->getConsumer('logs-printer');

$callback = function($message) {
    print_r($message->body);
};

$consumer->consume($callback);
```

And to start sending messages to exchanges, it is pretty much the same :

```php
$producer = $smq->getProducer('errors');
$message = json_encode(array('id' => 1234, 'text' => 'Hello world'));

$producer->sendMessage($message);
```

The producer and consumers knows exactly, based on the configuration, which routing key to use for direct and topic exchanges and
also which binding keys to use for binding queues to exchanges.


--- 

License: MIT

Author: [Christophe Willemsen](mailto:christophe@graphaware.com)
