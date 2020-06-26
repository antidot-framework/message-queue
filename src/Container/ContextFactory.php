<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Assert\Assertion;
use Enqueue\Dbal\DbalContext;
use Enqueue\Fs\FsConnectionFactory;
use Enqueue\Null\NullContext;
use Interop\Queue\Context;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

use function sprintf;

class ContextFactory
{
    private const NULL = 'null';
    private const FILESYSTEM = 'fs';
    private const DBAL = 'dbal';

    public function __invoke(
        ContainerInterface $container,
        string $contextName = ConfigProvider::DEFAULT_CONTEXT
    ): Context {
        $contextConfig = ConfigProvider::getContextConfig($contextName, $container->get(ConfigProvider::CONFIG_KEY));

        $contextType = $contextConfig[ConfigProvider::CONTEXTS_TYPE_KEY];
        if (self::NULL === $contextType) {
            return new NullContext();
        }

        if (self::FILESYSTEM === $contextType) {
            Assertion::classExists(
                FsConnectionFactory::class,
                'Install "enqueue/fs" package to run filesystem context.'
            );
            Assertion::keyExists($contextConfig, 'path', 'Absolute "path" is required to run filesystem context.');
            return (new FsConnectionFactory($contextConfig['path']))->createContext();
        }

        if (self::DBAL === $contextType) {
            Assertion::classExists(
                DbalContext::class,
                'Install "enqueue/fs" package to run filesystem context.'
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

        throw new InvalidArgumentException(sprintf('There is not implementation for given context %s.', $contextType));
    }
}
