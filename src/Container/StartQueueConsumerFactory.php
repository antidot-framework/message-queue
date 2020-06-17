<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Cli\StartQueueConsumer;
use Antidot\Queue\Container\Config\ConfigProvider;
use Enqueue\Consumption\QueueConsumerInterface;
use Psr\Container\ContainerInterface;

class StartQueueConsumerFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): StartQueueConsumer {
        return new StartQueueConsumer(
            $container->get(QueueConsumerInterface::class),
            $container->get($contextName . '.action.container')
        );
    }
}
