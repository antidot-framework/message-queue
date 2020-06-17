# Antidot Framework Message Queue

Message bus and Pub-Sub implementation using [enqueue/enqueue](https://github.com/php-enqueue/enqueue-dev) for Antidot Framework.
Check the list of the available extensions at [their docs](https://github.com/php-enqueue/enqueue-dev/blob/master/docs/client/supported_brokers.md).

## Message Queue

> A message queue is an asynchronous communication method. It allows storing messages in the queue system until they are consumed and destroyed. 
>Each message is processed only once by a unique consumer.

### Different Queue Systems

* Filesystem Queue
* Redis Queue

Each implementation will have different configuration details, see concrete documentation section. Furthermore, 
you can use any of [systems implemented in the php-enqueue package](https://php-enqueue.github.io/transport) making the needed factories.

### Usage

You can define as many contexts as you need, you can bind each context to different queues.
Once you have a context you can start sending jobs to the queue. 
The job should contain the queue name, the message type and the message itself.

```php
<?php

declare(strict_types=1);

/** @var \Psr\Container\ContainerInterface $container */
$producer = $container->get(\Antidot\Queue\Producer::class);
$producer->enqueue(Job::create('some_queue', 'some_message', 'Hola Mundo!!')); 
```

Start listening a queue

```bash
bin/console queue:start default # "default is the queue name"
```

Now you can configure actions for the message types received by the queue. 
The action is a callable class that receives a JobPayload as first parameter.

### Config


#### Bind an action to a message type.

```yaml
services:
  some_action_service:
    class: Some\Action\Class
parameters:
  queues:
    contexts:
      default:
        message_types:
          # message_type: action_service
          some_message: some_action_service
```

This is the default config.

```yaml
parameters:
  queues:
    contexts:
      default:
        message_types: []
        context: fs # redis
        context_service: queue.context.default
        container: queue.container.default
        extensions:
          - Enqueue\Consumption\Extension\LoggerExtension

```

#### Filesystem Config

#### Redis Config
