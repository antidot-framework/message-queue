<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Assert\Assertion;
use Enqueue\Dbal\DbalContext;
use Enqueue\Fs\FsConnectionFactory;
use Enqueue\Null\NullContext;
use Enqueue\Redis\RedisConnectionFactory;
use Enqueue\Sqs\SqsConnectionFactory;
use Interop\Queue\Context;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

use function array_key_exists;
use function sprintf;

class ContextFactory
{
    private const NULL = 'null';
    private const FILESYSTEM = 'fs';
    private const DBAL = 'dbal';
    private const REDIS = 'redis';
    private const SQS = 'sqs';

    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): Context {
        $contextConfig = ConfigProvider::getContextConfig($contextName, $container->get(ConfigProvider::CONFIG_KEY));

        $contextType = $contextConfig[ConfigProvider::CONTEXTS_TYPE_KEY];
        if (self::NULL === $contextType) {
            return new NullContext();
        }

        Assertion::keyExists($contextConfig, 'context_params');
        if (self::FILESYSTEM === $contextType) {
            return $this->createFilesystemContext($contextConfig['context_params']);
        }

        if (self::DBAL === $contextType) {
            return $this->createDBALContext($container, $contextConfig['context_params']);
        }

        if (self::REDIS === $contextType) {
            return $this->createRedisContext($contextConfig['context_params']);
        }

        if (self::SQS === $contextType) {
            return $this->createSQSContext($contextConfig['context_params']);
        }

        throw new InvalidArgumentException(sprintf('There is not implementation for given context %s.', $contextType));
    }

    private function createFilesystemContext(array $contextConfig): Context
    {
        Assertion::classExists(
            FsConnectionFactory::class,
            'Install "enqueue/fs" package to run filesystem context.'
        );
        Assertion::keyExists($contextConfig, 'path', 'Absolute "path" is required to run filesystem context.');

        return (new FsConnectionFactory($contextConfig['path']))->createContext();
    }

    private function createDBALContext(ContainerInterface $container, array $contextConfig): Context
    {
        Assertion::classExists(
            DbalContext::class,
            'Install "enqueue/dbal" package to run Doctrine DBAL context.'
        );
        Assertion::keyExists(
            $contextConfig,
            'connection',
            'The "connection" service name is required to run dbal context.'
        );
        $context = new DbalContext($container->get($contextConfig['connection']));
        $context->createDataBaseTable();

        return $context;
    }

    private function createRedisContext(array $contextConfig): Context
    {
        Assertion::classExists(
            RedisConnectionFactory::class,
            'Install "enqueue/redis" package to run redis context.'
        );
        Assertion::keyExists(
            $contextConfig,
            'host',
            'The "host" name is required to run redis context.'
        );
        Assertion::keyExists(
            $contextConfig,
            'port',
            'The "port" is required to run redis context.'
        );
        Assertion::keyExists(
            $contextConfig,
            'scheme_extensions',
            'The "scheme_extensions" name is required to run redis context.'
        );

        return (new RedisConnectionFactory($contextConfig))->createContext();
    }

    private function createSQSContext($contextConfig): Context
    {
        Assertion::classExists(
            SqsConnectionFactory::class,
            'Install "enqueue/sqs" package to run Amazon SQS context.'
        );
        Assertion::keyExists(
            $contextConfig,
            'key',
            'The AWS "key" is required to run Amazon SQS context.'
        );
        Assertion::keyExists(
            $contextConfig,
            'secret',
            'The AWS "secret" is required to run Amazon SQS context.'
        );
        Assertion::keyExists(
            $contextConfig,
            'region',
            'The AWS "region" is required to run Amazon SQS context.'
        );

        return (new SqsConnectionFactory($contextConfig))->createContext();
    }
}
