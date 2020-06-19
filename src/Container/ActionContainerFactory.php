<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\ActionContainer;
use Antidot\Queue\Container\Config\ConfigProvider;
use Assert\AssertionFailedException;
use Psr\Container\ContainerInterface;

class ActionContainerFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $contextName
     * @return ActionContainer
     * @throws AssertionFailedException
     */
    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): ActionContainer {
        $contextConfig = ConfigProvider::getContextConfig($contextName, $container->get(ConfigProvider::CONFIG_KEY));
        foreach ($contextConfig[ConfigProvider::MESSAGE_TYPES_KEY] ?? [] as $messageType => $action) {
            $actions[$messageType] = static fn() => $container->get($action);
        }

        return new ActionContainer($actions ?? []);
    }
}
