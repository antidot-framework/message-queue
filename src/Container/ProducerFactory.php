<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Antidot\Queue\JobProducer;
use Antidot\Queue\Producer;
use Interop\Queue\Context;
use Psr\Container\ContainerInterface;

class ProducerFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): Producer {
        /** @var array<string, array<string, mixed>> $config */
        $config = $container->get(ConfigProvider::CONFIG_KEY);
        $contextConfig = ConfigProvider::getContextConfig($contextName, $config);
        /** @var string $contaxtServiceName */
        $contaxtServiceName = $contextConfig[ConfigProvider::CONTEXT_SERVICE_KEY];
        /** @var Context $context */
        $context = $container->get($contaxtServiceName);

        return new JobProducer($context);
    }
}
