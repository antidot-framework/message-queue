<?php

declare(strict_types=1);

namespace Antidot\Queue\Container\Config;

use Antidot\Queue\Cli\StartQueueConsumer;
use Antidot\Queue\Container\ActionContainerFactory;
use Antidot\Queue\Container\MessageProcessorFactory;
use Antidot\Queue\Container\ProducerFactory;
use Antidot\Queue\Container\StartQueueConsumerFactory;
use Assert\Assertion;
use Assert\AssertionFailedException;

use function sprintf;

class ConfigProvider
{
    public const CONFIG_KEY = 'config';
    public const QUEUES_KEY = 'queues';
    public const CONTEXTS_KEY = 'contexts';
    public const DEFAULT_CONTEXT = 'default';
    public const MESSAGE_TYPES_KEY = 'message_types';
    public const INVALID_CONFIG_ARRAY_MESSAGE = 'Value for key "%s" must be of type array.';
    public const MISSING_CONFIG_MESSAGE = 'Missing config required key "%s", see the docs for complete config.';
    public const INVALID_CONTEXT_MESSAGE = 'Given context "%s" is not properly defined in the config.';
    public const DEFAULT_CONFIG = [
        self::QUEUES_KEY => [
            self::CONTEXTS_KEY => [
                self::DEFAULT_CONTEXT => [],
            ],
        ],
        'factories' => [
            self::DEFAULT_CONTEXT . '.action.container' => ActionContainerFactory::class,
            self::DEFAULT_CONTEXT . '.message.processor' => MessageProcessorFactory::class,
            self::DEFAULT_CONTEXT . '.message.producer' => ProducerFactory::class,
        ],
        'console' => [
            'commands' => [
                StartQueueConsumer::NAME => StartQueueConsumer::class,
            ],
            'factories' => [
                StartQueueConsumer::class => StartQueueConsumerFactory::class,
            ]
        ]
    ];

    public function __invoke(): array
    {
        return self::DEFAULT_CONFIG;
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
