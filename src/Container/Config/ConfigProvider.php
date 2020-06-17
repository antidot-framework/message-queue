<?php

declare(strict_types=1);

namespace Antidot\Queue\Container\Config;

use Antidot\Queue\Cli\StartQueueConsumer;
use Antidot\Queue\Container\ActionContainerFactory;
use Antidot\Queue\Container\MessageProcessorFactory;
use Antidot\Queue\Container\ProducerFactory;
use Antidot\Queue\Container\StartQueueConsumerFactory;
use Antidot\Queue\MessageProcessor;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Enqueue\Consumption\QueueConsumer;
use Enqueue\Consumption\QueueConsumerInterface;
use Enqueue\Null\NullContext;
use Interop\Queue\Context;
use Interop\Queue\Producer;

use InvalidArgumentException;

use function sprintf;

class ConfigProvider
{
    public const CONFIG_KEY = 'config';
    public const QUEUES_KEY = 'queues';
    public const CONTAINER_KEY = 'container';
    public const CONTEXTS_KEY = 'contexts';
    public const CONTEXTS_TYPE_KEY = 'context_type';
    public const DEFAULT_CONTEXT_TYPE = 'null';
    public const DEFAULT_CONTEXT_KEY = 'default_context';
    public const CONTEXT_SERVICE_KEY = 'context_service';
    public const DEFAULT_CONTEXT = 'default';
    public const DEFAULT_CONTEXT_SERVICE = 'queue.context.default';
    public const DEFAULT_CONTAINER_SERVICE = 'queue.container.default';
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
                ],
            ],
        ],
        'factories' => [
            self::DEFAULT_CONTEXT . '.action.container' => ActionContainerFactory::class,
            MessageProcessor::class => MessageProcessorFactory::class,
            Producer::class => ProducerFactory::class,
        ],
        'services' => [
            'null.context' => NullContext::class,
        ],
        'console' => [
            'commands' => [
                StartQueueConsumer::NAME => StartQueueConsumer::class,
            ],
            'factories' => [
                StartQueueConsumer::class => StartQueueConsumerFactory::class,
            ],
            'services' => [
                QueueConsumerInterface::class => QueueConsumer::class,
            ]
        ],
    ];

    public function __invoke(): array
    {
        return self::DEFAULT_CONFIG;
    }

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
     * @param array $config
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
