# Antidot Framework Message Queue

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/antidot-framework/message-queue/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/antidot-framework/message-queue/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/antidot-framework/message-queue/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/antidot-framework/message-queue/?branch=master)
[![Type Coverage](https://shepherd.dev/github/antidot-framework/message-queue/coverage.svg)](https://shepherd.dev/github/antidot-framework/message-queue)
[![Psalm Level](https://shepherd.dev/github/antidot-framework/message-queue/level.svg)](https://shepherd.dev/github/antidot-framework/message-queue)
[![Build Status](https://scrutinizer-ci.com/g/antidot-framework/message-queue/badges/build.png?b=master)](https://scrutinizer-ci.com/g/antidot-framework/message-queue/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/antidot-framework/message-queue/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Message queue implementation using [enqueue/enqueue](https://github.com/php-enqueue/enqueue-dev) for Antidot Framework.

```bash
composer require antidot-fw/message-queue
```

## Message Queue

> A message queue is an asynchronous communication method. It allows storing messages in the queue system until they are consumed and destroyed. 
>Each message is processed only once by a unique consumer.

### Different Queue Systems

* Null Queue
* Filesystem Queue
* DBAL Queue
* Redis Queue
* Beanstalk
* Amazon SQS

Each implementation will have different configuration details, see concrete documentation section. Furthermore, 
you can use any of [systems implemented in the PHP-enqueue package](https://php-enqueue.github.io/transport), making the needed factories.

### Usage

You can define as many contexts as you need. You can bind each context to different queues.
Once you have created a Context class, you can start sending jobs to the queue. 
The job should contain the queue name, the message type, and the message itself.

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
The action is a callable class that receives a JobPayload as the first parameter.

### Jobs and Producer

A Job is a class responsible for transport given data to the queue. It is composed of two parameters: the Queue name as a single string, 
and the JobPayload with the data to process in the queue. JsonPayload is a JSON serializable object composed of two other parameters:
the message type and the message data as string or array.

Once you have a job class, you need to pass it to the producer to send the message to the queue. See the example below.
```php
<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Antidot\Queue\Producer;

/** @var ContainerInterface $container */
/** @var Producer $producer */
$producer = $container->get(Producer::class);

// Send String Job of type "some_message_type" to "default" queue.
$job1 = Job::create('default', 'some_message_type', 'Hello world!!');
$producer->enqueue($job1);

// Send Array Job of type "other_message_type" to "other_queue" queue.
$job2 = Job::create('other_queue', 'other_message_type', ['greet' => 'Hello world!!']);
$producer->enqueue($job2);

```

### Actions

The actions are invokable classes that will execute when the queue processes the given message. This class has a unique parameter, the JobPayload. 

```php
<?php

declare(strict_types=1);

use Antidot\Queue\JobPayload;

class SomeMessageTypeAction
{
    public function __invoke(JobPayload $payload): void
    {
        // do some stuff with the job payload here.
    }
}
```

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
    default_context: default
    contexts:
      default:
        message_types: []
        context_type: fs # redis|dbal|sqs|beanstalk|null
        context_service: queue.context.default
        container: queue.container.default
        extensions:
          - Enqueue\Consumption\Extension\LoggerExtension
          - Enqueue\Consumption\Extension\SignalExtension
          - Enqueue\Consumption\Extension\LogExtension
```

### Transport specific config

#### Null Queue

So util for testing purposes, it discards any received job. The only configuration required by this transport type is to set it as context.

```bash
composer require enqueue/null
```

#### Filesystem Queue

The Filesystem queue stores produced jobs inside a file in memory. It requires the absolute file path to store the jobs.

```bash
composer require enqueue/fs
```

```yaml
parameters:
  queues:
    default_context: default
    contexts:
      default:
        message_types: []
        context_type: fs
        context_params:
          path: file:///absoute/path/to/writable/dir
```

#### DBAL Queue

The Doctrine DBAL queue stores produced jobs inside a database. It requires the name of the DBAL connection service.

```bash
composer require enqueue/dbal
```

```yaml
parameters:
  queues:
    default_context: default
    contexts:
      default:
        message_types: []
        context_type: dbal
        context_params:
          connection: Doctrine\DBAL\Connection
```

#### Redis Queue

The redis queue stores produced jobs inside a redis database. It requires the redis connection params.
You can use it with [Predis](https://github.com/nrk/predis) or with the [PHP Redis extension](https://github.com/phpredis/phpredis).

With Predis:
  
```bash
composer require enqueue/redis predis/predis:^1
```

```yaml
parameters:
  queues:
    default_context: default
    contexts:
      default:
        message_types: []
        context_type: redis
        context_params:
            host: localhost
            port: 6379
            scheme_extensions: ['predis']
```

With PHP extension:
 
Ensure that you have PHP Redis extension installed and enabled
   
```bash
composer require enqueue/redis
```

```yaml
parameters:
  queues:
    default_context: default
    contexts:
      default:
        message_types: []
        context_type: redis
            host: localhost
            port: 6379
            scheme_extensions: ['phpredis']
```

#### Beanstalk Queue

The Beanstalk queue requires the beanstalk host and beanstalk port to work. It uses Pheanstalk PHP library.

```bash
composer require enqueue/pheanstalk
```

```yaml
parameters:
  queues:
    default_context: default
    contexts:
      default:
        message_types: []
        context_type: beanstalk
        context_params:
          host: localhost
          port: 11300
```

#### Amazon SQS Queue

The Amazon SQS queue stores produced jobs at aws. It requires the AWS console credentials. 
You can use both [standard and FIFO queues](https://aws.amazon.com/es/sqs/) depending on your requirements. The queue must exist in AWS before sending a Job to work.

```bash
composer require enqueue/sqs
```

```yaml
parameters:
  queues:
    default_context: default
    contexts:
      default:
        message_types: []
        context_type: sqs
        context_params:
          key: AWS-KEY
          secret: AWS-SECRET
          region: eu-west-3
```

### Consumer

The worker is the CLI command responsible for listening to the given queue to get messages and process each message one by one. 
In this early version, the only argument that it uses is the queue name to start listening.

```bash
bin/console queue:start queue_name
```

### Events

The Antidot Framework Message Queue uses the [PSR-14 Event Dispatcher](https://www.php-fig.org/psr/psr-14/) to allow listening different instant occurred in the queue execution:

* **QueueConsumeWasStarted:** Dispatches on queue start.
* **MessageReceived:** Dispatches for any received job before process.
* **MessageProcessed:** Dispatches after the job processed, it will have the result of the process.


### Extensions

See more about extensions on [php-enqueue official docs](https://php-enqueue.github.io/consumption/extensions/)

#### LogExtension

You can enable or disable debug mode logger in the framework default config. it uses PSR-3 Logger Interface internally.

### Running in Production

In the production environment, you usually need a daemon to keep the consumer process alive. You can use Supervisor or any other system daemon alternative.

##### Supervisor

You need to install [supervidor](http://supervisord.org/installing.html) in your system. Then you need to configure the consumer as a supervisor job.

```bash
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /absolute/path/to/app/bin/console queue:start QUEUE_NAME
autostart=true
autorestart=true
user=ubuntu
numprocs=2 # Be cautious, it will block your computer depending on the available simultaneous execution thread it has.
redirect_stderr=true
stdout_logfile=/absolute/path/to/app/var/log/worker.log
stopwaitsecs=3600
```
