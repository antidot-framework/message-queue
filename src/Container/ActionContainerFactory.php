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
        /** @var array<string, array<string, mixed>> $config */
        $config = $container->get(ConfigProvider::CONFIG_KEY);
        $contextConfig = ConfigProvider::getContextConfig($contextName, $config);
        $actions = [];
        /** @var array<string, string> $messages */
        $messages = $contextConfig[ConfigProvider::MESSAGE_TYPES_KEY] ?? [];
        foreach ($messages as $messageType => $action) {
            $actions[$messageType] = static function () use ($container, $action): callable {
                /** @var callable $actionCallable */
                $actionCallable = $container->get($action);
                return $actionCallable;
            };
        }

        return new ActionContainer($actions);
    }
}
