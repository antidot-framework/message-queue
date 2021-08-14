<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\ActionContainer;
use Antidot\Queue\Container\Config\ConfigProvider;
use Antidot\Queue\MessageProcessor;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class MessageProcessorFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): MessageProcessor {
        /** @var array<string, array<string, mixed>> $config */
        $config = $container->get(ConfigProvider::CONFIG_KEY);
        $contextConfig = ConfigProvider::getContextConfig($contextName, $config);
        /** @var string $actionContainerName */
        $actionContainerName = $contextConfig[ConfigProvider::CONTAINER_KEY];
        /** @var ActionContainer $actionContainer */
        $actionContainer = $container->get($actionContainerName);
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $container->get(EventDispatcherInterface::class);

        return new MessageProcessor($actionContainer, $eventDispatcher);
    }
}
