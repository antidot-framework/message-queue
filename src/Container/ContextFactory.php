<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Assert\Assertion;
use Doctrine\DBAL\Connection;
use Enqueue\Dbal\DbalContext;
use Enqueue\Fs\FsConnectionFactory;
use Enqueue\Null\NullContext;
use Enqueue\Pheanstalk\PheanstalkConnectionFactory;
use Enqueue\Redis\RedisConnectionFactory;
use Enqueue\Sqs\SqsConnectionFactory;
use Interop\Queue\Context;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

use function sprintf;

class ContextFactory
{
    private const NULL = 'null';
    private const FILESYSTEM = 'fs';
    private const DBAL = 'dbal';
    private const REDIS = 'redis';
    private const SQS = 'sqs';
    private const BEANSTALK = 'beanstalk';

    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): Context {
        /** @var array<string, array<string, mixed>> $config */
        $config = $container->get(ConfigProvider::CONFIG_KEY);
        $contextConfig = ConfigProvider::getContextConfig($contextName, $config);

        /** @var string $contextType */
        $contextType = $contextConfig[ConfigProvider::CONTEXTS_TYPE_KEY];
        if (self::NULL === $contextType) {
            return new NullContext();
        }

        Assertion::keyExists($contextConfig, 'context_params');
        /** @var array<string, mixed> $contextParams */
        $contextParams = $contextConfig['context_params'];
        if (self::FILESYSTEM === $contextType) {
            return $this->createFilesystemContext($contextParams);
        }

        if (self::DBAL === $contextType) {
            return $this->createDBALContext($container, $contextParams);
        }

        if (self::REDIS === $contextType) {
            return $this->createRedisContext($contextParams);
        }

        if (self::BEANSTALK === $contextType) {
            return $this->createBeanstalkContext($contextParams);
        }

        if (self::SQS === $contextType) {
            return $this->createSQSContext($contextParams);
        }

        throw new InvalidArgumentException(sprintf('There is not implementation for given context %s.', $contextType));
    }

    /**
     * @param array<mixed> $contextConfig
     * @throws \Assert\AssertionFailedException
     */
    private function createFilesystemContext(array $contextConfig): Context
    {
        Assertion::classExists(
            FsConnectionFactory::class,
            'Install "enqueue/fs" package to run filesystem context.'
        );
        Assertion::keyExists($contextConfig, 'path', 'Absolute "path" is required to run filesystem context.');
        $contextPath = $contextConfig['path'];
        Assertion::string($contextPath, 'Absolute "path" must be string.');

        return (new FsConnectionFactory($contextPath))->createContext();
    }

    /**
     * @param array<mixed> $contextConfig
     * @throws \Assert\AssertionFailedException
     */
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
        /** @var string $connectionName */
        $connectionName = $contextConfig['connection'];
        /** @var Connection $connection */
        $connection = $container->get($connectionName);
        $context = new DbalContext($connection);
        $context->createDataBaseTable();

        return $context;
    }

    /**
     * @param array<mixed> $contextConfig
     * @throws \Assert\AssertionFailedException
     */
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

    /**
     * @param array<mixed> $contextConfig
     * @throws \Assert\AssertionFailedException
     */
    private function createSQSContext(array $contextConfig): Context
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

    /**
     * @param array<mixed> $contextConfig
     * @throws \Assert\AssertionFailedException
     */
    private function createBeanstalkContext(array $contextConfig): Context
    {
        Assertion::classExists(
            RedisConnectionFactory::class,
            'Install "enqueue/pheanstalk" package to run beanstalk context.'
        );
        Assertion::keyExists(
            $contextConfig,
            'host',
            'The "host" name is required to run beanstalk context.'
        );
        Assertion::keyExists(
            $contextConfig,
            'port',
            'The "port" is required to run beanstalk context.'
        );

        return (new PheanstalkConnectionFactory($contextConfig))->createContext();
    }
}
