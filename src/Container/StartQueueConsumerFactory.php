<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Cli\StartQueueConsumer;
use Antidot\Queue\Container\Config\ConfigProvider;
use Enqueue\Consumption\QueueConsumerInterface;
use Interop\Queue\Processor;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class StartQueueConsumerFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): StartQueueConsumer {
        $contextConfig = ConfigProvider::getContextConfig($contextName, $container->get(ConfigProvider::CONFIG_KEY));

        return new StartQueueConsumer(
            $container->get(QueueConsumerInterface::class),
            $container->get(Processor::class),
            $container->get($contextConfig[ConfigProvider::CONTEXT_SERVICE_KEY]),
            $container->get(EventDispatcherInterface::class)
        );
    }
}
