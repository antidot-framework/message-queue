<?php

declare(strict_types=1);

namespace Antidot\Queue\Container\Config;

use Antidot\Queue\Cli\StartQueueConsumer;
use Antidot\Queue\Container\ActionContainerFactory;
use Antidot\Queue\Container\ContextFactory;
use Antidot\Queue\Container\LoggerExtensionFactory;
use Antidot\Queue\Container\MessageProcessorFactory;
use Antidot\Queue\Container\ProducerFactory;
use Antidot\Queue\Container\QueueConsumerFactory;
use Antidot\Queue\Container\StartQueueConsumerFactory;
use Antidot\Queue\Producer;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Enqueue\Consumption\Extension\LogExtension;
use Enqueue\Consumption\Extension\LoggerExtension;
use Enqueue\Consumption\Extension\SignalExtension;
use Enqueue\Consumption\QueueConsumerInterface;
use Enqueue\Null\NullContext;
use Interop\Queue\Context;
use Interop\Queue\Processor;
use InvalidArgumentException;

use function sprintf;

class ConfigProvider
{
    public const CONFIG_KEY = 'config';
    public const QUEUES_KEY = 'queues';
    public const CONTAINER_KEY = 'container';
    public const CONTEXTS_KEY = 'contexts';
    public const CONTEXT_SERVICE_KEY = 'context_service';
    public const CONTEXTS_TYPE_KEY = 'context_type';
    public const DEFAULT_CONTEXT_TYPE = 'null';
    public const DEFAULT_CONTEXT_KEY = 'default_context';
    public const EXTENSIONS_KEY = 'extensions';
    public const DEFAULT_CONTEXT = 'default';
    public const DEFAULT_CONTEXT_SERVICE = 'queue.context.default';
    public const DEFAULT_CONTAINER_SERVICE = 'queue.container.default';
    public const DEFAULT_EXTENSIONS = [
        LoggerExtension::class,
        SignalExtension::class,
        LogExtension::class,
    ];
    public const MESSAGE_TYPES_KEY = 'message_types';
    public const INVALID_CONFIG_ARRAY_MESSAGE = 'Value for key "%s" must be of type array.';
    public const MISSING_CONFIG_MESSAGE = 'Missing config required key "%s", see the docs for complete config.';
    public const INVALID_CONTEXT_MESSAGE = 'Given context "%s" is not properly defined in the config.';
    public const DEFAULT_CONFIG = [
        self::QUEUES_KEY => [
            self::DEFAULT_CONTEXT_KEY => self::DEFAULT_CONTEXT,
            self::CONTEXTS_KEY => [
                self::DEFAULT_CONTEXT => [
                    self::CONTEXTS_TYPE_KEY => self::DEFAULT_CONTEXT_TYPE,
                    self::CONTEXT_SERVICE_KEY => self::DEFAULT_CONTEXT_SERVICE,
                    self::CONTAINER_KEY => self::DEFAULT_CONTAINER_SERVICE,
                    self::EXTENSIONS_KEY => self::DEFAULT_EXTENSIONS,
                ],
            ],
        ],
        'factories' => [
            self::DEFAULT_CONTAINER_SERVICE => ActionContainerFactory::class,
            self::DEFAULT_CONTEXT_SERVICE => ContextFactory::class,
            Processor::class => MessageProcessorFactory::class,
            Producer::class => ProducerFactory::class,
            LoggerExtension::class => LoggerExtensionFactory::class,
        ],
        'services' => [
            Context::class => self::DEFAULT_CONTEXT_SERVICE,
            SignalExtension::class => SignalExtension::class,
            LogExtension::class => LogExtension::class,
        ],
        'console' => [
            'commands' => [
                StartQueueConsumer::NAME => StartQueueConsumer::class,
            ],
            'factories' => [
                StartQueueConsumer::class => StartQueueConsumerFactory::class,
                QueueConsumerInterface::class => QueueConsumerFactory::class,
            ],
        ],
    ];

    /**
     * @return array<mixed>
     */
    public function __invoke(): array
    {
        return self::DEFAULT_CONFIG;
    }

    /**
     * @param array<mixed> $config
     * @return array<mixed>
     * @throws AssertionFailedException
     */
    public static function getContextConfig(string $contextName, array $config): array
    {
        ConfigProvider::validate($config);

        $contextsConfig = $config[ConfigProvider::QUEUES_KEY][ConfigProvider::CONTEXTS_KEY];
        if (false === array_key_exists($contextName, $contextsConfig)) {
            throw new InvalidArgumentException(sprintf(ConfigProvider::INVALID_CONTEXT_MESSAGE, $contextName));
        }

        return $contextsConfig[$contextName];
    }


    /**
     * @param array<mixed> $config
     * @throws AssertionFailedException
     */
    public static function validate(array $config): void
    {
        Assertion::keyExists($config, self::QUEUES_KEY, sprintf(self::MISSING_CONFIG_MESSAGE, self::QUEUES_KEY));
        Assertion::isArray($config[self::QUEUES_KEY], sprintf(self::INVALID_CONFIG_ARRAY_MESSAGE, self::QUEUES_KEY));
        Assertion::keyExists(
            $config[self::QUEUES_KEY],
            self::CONTEXTS_KEY,
            sprintf(self::MISSING_CONFIG_MESSAGE, self::CONTEXTS_KEY)
        );
    }
}
