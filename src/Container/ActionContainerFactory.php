<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\ActionContainer;
use Antidot\Queue\Container\Config\ConfigProvider;
use Assert\AssertionFailedException;
use InvalidArgumentException;
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
        $config = $container->get(ConfigProvider::CONFIG_KEY);
        ConfigProvider::validate($config);

        $contextsConfig = $config[ConfigProvider::QUEUES_KEY][ConfigProvider::CONTEXTS_KEY];
        if (false === array_key_exists($contextName, $contextsConfig)) {
            throw new InvalidArgumentException(sprintf(ConfigProvider::INVALID_CONTEXT_MESSAGE, $contextName));
        }

        $contextConfig = $contextsConfig[$contextName];
        foreach ($contextConfig[ConfigProvider::MESSAGE_TYPES_KEY] ?? [] as $messageType => $action) {
            $actions[$messageType] = static fn(): callable => $container->get($action);
        }

        return new ActionContainer($actions ?? []);
    }
}
