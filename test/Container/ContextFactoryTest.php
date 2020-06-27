<?php

declare(strict_types=1);


namespace AntidotTest\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Antidot\Queue\Container\ContextFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Enqueue\Dbal\DbalContext;
use Enqueue\Fs\FsContext;
use Enqueue\Null\NullContext;
use Enqueue\Pheanstalk\PheanstalkContext;
use Enqueue\Redis\RedisContext;
use Enqueue\Sqs\SqsContext;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function array_merge;

class ContextFactoryTest extends TestCase
{
    public function testItShouldThrowAnExceptionIfGivenContextTypeHasNotImplementation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $config = array_merge(
            ConfigProvider::DEFAULT_CONFIG,
            [
                'queues' => [
                    'contexts' => [
                        ConfigProvider::DEFAULT_CONTEXT => [
                            ConfigProvider::CONTEXTS_TYPE_KEY => 'amqp',
                            'context_params' => [],
                        ],
                    ],
                ],
            ]
        );
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ConfigProvider::CONFIG_KEY)
            ->willReturn($config);

        $factory = new ContextFactory();
        $factory->__invoke($container);
    }

    public function testItShouldCreateInstancesOfNullContextIfConfigured(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ConfigProvider::CONFIG_KEY)
            ->willReturn(ConfigProvider::DEFAULT_CONFIG);

        $factory = new ContextFactory();
        $this->assertInstanceOf(NullContext::class, $factory->__invoke($container));
    }

    public function testItShouldCreateInstancesOfFSContextIfConfigured(): void
    {
        $config = array_merge(
            ConfigProvider::DEFAULT_CONFIG,
            [
                'queues' => [
                    'contexts' => [
                        ConfigProvider::DEFAULT_CONTEXT => [
                            ConfigProvider::CONTEXTS_TYPE_KEY => 'fs',
                            'context_params' => [
                                'path' => '/tmp/queue',
                            ]
                        ],
                    ],
                ],
            ]
        );
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ConfigProvider::CONFIG_KEY)
            ->willReturn($config);

        $factory = new ContextFactory();
        $this->assertInstanceOf(FsContext::class, $factory->__invoke($container));
    }

    public function testItShouldCreateInstancesOfDBALContextIfConfigured(): void
    {
        $config = array_merge(
            ConfigProvider::DEFAULT_CONFIG,
            [
                'queues' => [
                    'contexts' => [
                        ConfigProvider::DEFAULT_CONTEXT => [
                            ConfigProvider::CONTEXTS_TYPE_KEY => 'dbal',
                            'context_params' => [
                                'connection' => Connection::class,
                            ]
                        ],
                    ],
                ],
            ]
        );
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('getSchemaManager')
            ->willReturn($this->createMock(AbstractSchemaManager::class));
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([ConfigProvider::CONFIG_KEY], [Connection::class])
            ->willReturnOnConsecutiveCalls($config, $connection);

        $factory = new ContextFactory();
        $this->assertInstanceOf(DbalContext::class, $factory->__invoke($container));
    }

    public function testItShouldCreateInstancesOfRedisContextIfConfigured(): void
    {
        $config = array_merge(
            ConfigProvider::DEFAULT_CONFIG,
            [
                'queues' => [
                    'contexts' => [
                        ConfigProvider::DEFAULT_CONTEXT => [
                            ConfigProvider::CONTEXTS_TYPE_KEY => 'redis',
                            'context_params' => [
                                'host' => 'localhost',
                                'port' => 6379,
                                'scheme_extensions' => ['predis'],
                            ],
                        ],
                    ],
                ],
            ]
        );
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ConfigProvider::CONFIG_KEY)
            ->willReturn($config);

        $factory = new ContextFactory();
        $this->assertInstanceOf(RedisContext::class, $factory->__invoke($container));
    }

    public function testItShouldCreateInstancesOfBeanstalkContextIfConfigured(): void
    {
        $config = array_merge(
            ConfigProvider::DEFAULT_CONFIG,
            [
                'queues' => [
                    'contexts' => [
                        ConfigProvider::DEFAULT_CONTEXT => [
                            ConfigProvider::CONTEXTS_TYPE_KEY => 'beanstalk',
                            'context_params' => [
                                'host' => 'localhost',
                                'port' => 5555,
                            ],
                        ],
                    ],
                ],
            ]
        );
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ConfigProvider::CONFIG_KEY)
            ->willReturn($config);

        $factory = new ContextFactory();
        $this->assertInstanceOf(PheanstalkContext::class, $factory->__invoke($container));
    }

    public function testItShouldCreateInstancesOfSQSContextIfConfigured(): void
    {
        $config = array_merge(
            ConfigProvider::DEFAULT_CONFIG,
            [
                'queues' => [
                    'contexts' => [
                        ConfigProvider::DEFAULT_CONTEXT => [
                            ConfigProvider::CONTEXTS_TYPE_KEY => 'sqs',
                            'context_params' => [
                                'key' => 'AWS-KEY',
                                'secret' => 'AWS-SECRET',
                                'region' => 'eu-west-3',
                            ],
                        ],
                    ],
                ],
            ]
        );
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ConfigProvider::CONFIG_KEY)
            ->willReturn($config);

        $factory = new ContextFactory();
        $this->assertInstanceOf(SqsContext::class, $factory->__invoke($container));
    }
}
