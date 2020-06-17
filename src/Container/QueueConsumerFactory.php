<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Enqueue\Consumption\ChainExtension;
use Enqueue\Consumption\QueueConsumer;
use Enqueue\Consumption\QueueConsumerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class QueueConsumerFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): QueueConsumerInterface {
        $contextConfig = ConfigProvider::getContextConfig($contextName, $container->get(ConfigProvider::CONFIG_KEY));

        $extensions = [];
        foreach ($contextConfig['extensions'] ?? [] as $extensionName) {
            $extensions[] = $container->get($extensionName);
        }

        return new QueueConsumer(
            $container->get($contextConfig[ConfigProvider::CONTEXT_SERVICE_KEY]),
            new ChainExtension($extensions),
            [],
            $container->get(LoggerInterface::class)
        );
    }
}
