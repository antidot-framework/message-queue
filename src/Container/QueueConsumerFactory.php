<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Enqueue\Consumption\ChainExtension;
use Enqueue\Consumption\EndExtensionInterface;
use Enqueue\Consumption\ExtensionInterface;
use Enqueue\Consumption\InitLoggerExtensionInterface;
use Enqueue\Consumption\MessageReceivedExtensionInterface;
use Enqueue\Consumption\MessageResultExtensionInterface;
use Enqueue\Consumption\PostConsumeExtensionInterface;
use Enqueue\Consumption\PostMessageReceivedExtensionInterface;
use Enqueue\Consumption\PreConsumeExtensionInterface;
use Enqueue\Consumption\PreSubscribeExtensionInterface;
use Enqueue\Consumption\ProcessorExceptionExtensionInterface;
use Enqueue\Consumption\QueueConsumer;
use Enqueue\Consumption\QueueConsumerInterface;
use Enqueue\Consumption\StartExtensionInterface;
use Interop\Queue\Context;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class QueueConsumerFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): QueueConsumerInterface {
        /** @var array<string, array<string, mixed>> $config */
        $config = $container->get(ConfigProvider::CONFIG_KEY);
        $contextConfig = ConfigProvider::getContextConfig($contextName, $config);

        /** @var array<string> $contextExtensions */
        $contextExtensions = $contextConfig['extensions'] ?? [];
        $extensions = [];
        foreach ($contextExtensions as $extension) {
            /**
             * @var ExtensionInterface|StartExtensionInterface|PreSubscribeExtensionInterface
             *      |PreConsumeExtensionInterface|MessageReceivedExtensionInterface
             *      |PostMessageReceivedExtensionInterface|MessageResultExtensionInterface
             *      |ProcessorExceptionExtensionInterface|PostConsumeExtensionInterface|EndExtensionInterface
             *      |InitLoggerExtensionInterface $extensionService
             */
            $extensionService = $container->get($extension);
            $extensions[] = $extensionService;
        }
        /** @var string $contextServiceName */
        $contextServiceName = $contextConfig[ConfigProvider::CONTEXT_SERVICE_KEY];
        /** @var Context $contextService */
        $contextService = $container->get($contextServiceName);
        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);

        return new QueueConsumer($contextService, new ChainExtension($extensions), [], $logger);
    }
}
