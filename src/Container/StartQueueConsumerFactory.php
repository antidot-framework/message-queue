<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Cli\StartQueueConsumer;
use Antidot\Queue\Container\Config\ConfigProvider;
use Enqueue\Consumption\QueueConsumerInterface;
use Interop\Queue\Context;
use Interop\Queue\Processor;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class StartQueueConsumerFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): StartQueueConsumer {
        /** @var array<string, array<string, mixed>> $config */
        $config = $container->get(ConfigProvider::CONFIG_KEY);
        $contextConfig = ConfigProvider::getContextConfig($contextName, $config);
        /** @var QueueConsumerInterface $queueConsumerInterface */
        $queueConsumerInterface = $container->get(QueueConsumerInterface::class);
        /** @var Processor $processor */
        $processor = $container->get(Processor::class);
        /** @var string $contextServiceName */
        $contextServiceName = $contextConfig[ConfigProvider::CONTEXT_SERVICE_KEY];
        /** @var Context $contextService */
        $contextService = $container->get($contextServiceName);
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $container->get(EventDispatcherInterface::class);

        return new StartQueueConsumer(
            $queueConsumerInterface,
            $processor,
            $contextService,
            $eventDispatcher
        );
    }
}
