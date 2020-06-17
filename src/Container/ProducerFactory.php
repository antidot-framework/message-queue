<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Antidot\Queue\JobProducer;
use Antidot\Queue\Producer;
use Psr\Container\ContainerInterface;

class ProducerFactory
{
    private const SERVICE_SUFFIX = '.context';

    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): Producer {
        $contextConfig = ConfigProvider::getContextConfig($contextName, $container->get(ConfigProvider::CONFIG_KEY));
        $context = $container->get($contextConfig[ConfigProvider::CONTEXTS_TYPE_KEY] . self::SERVICE_SUFFIX);

        return new JobProducer($context);
    }
}
