<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Antidot\Queue\MessageProcessor;
use Psr\Container\ContainerInterface;

class MessageProcessorFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): MessageProcessor {
        $contextConfig = ConfigProvider::getContextConfig($contextName, $container->get(ConfigProvider::CONFIG_KEY));
        $actionContainer = $container->get($contextConfig[ConfigProvider::CONTAINER_KEY]);

        return new MessageProcessor($actionContainer);
    }
}
