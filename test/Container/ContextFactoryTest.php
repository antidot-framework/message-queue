<?php

declare(strict_types=1);


namespace AntidotTest\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Antidot\Queue\Container\ContextFactory;
use Doctrine\DBAL\Connection;
use Enqueue\Dbal\DbalContext;
use Enqueue\Fs\FsContext;
use Enqueue\Null\NullContext;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function array_merge;

class ContextFactoryTest extends TestCase
{
    public function testItShouldThrowAnExceptionIfGivenContextTypeHasNotImplementation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $config = array_merge(ConfigProvider::DEFAULT_CONFIG, [
            'queues' => [
                'contexts' => [
                    ConfigProvider::DEFAULT_CONTEXT => [
                        ConfigProvider::CONTEXTS_TYPE_KEY => 'amqp',
                    ],
                ],
            ],
        ]);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ConfigProvider::CONFIG_KEY)
            ->willReturn($config);

        $factory = new ContextFactory();
        $factory->__invoke($container);
    }

    public function testItShouldCreateInstancesOfNullContextIdConfigured(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ConfigProvider::CONFIG_KEY)
            ->willReturn(ConfigProvider::DEFAULT_CONFIG);

        $factory = new ContextFactory();
        $this->assertInstanceOf(NullContext::class, $factory->__invoke($container));
    }

    public function testItShouldCreateInstancesOfFSContextIdConfigured(): void
    {
        $config = array_merge(ConfigProvider::DEFAULT_CONFIG, [
            'queues' => [
                'contexts' => [
                    ConfigProvider::DEFAULT_CONTEXT => [
                        ConfigProvider::CONTEXTS_TYPE_KEY => 'fs',
                        'path' => '/tmp/queue',
                    ],
                ],
            ],
        ]);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ConfigProvider::CONFIG_KEY)
            ->willReturn($config);

        $factory = new ContextFactory();
        $this->assertInstanceOf(FsContext::class, $factory->__invoke($container));
    }

    public function testItShouldCreateInstancesOfDBALContextIdConfigured(): void
    {
        $config = array_merge(ConfigProvider::DEFAULT_CONFIG, [
            'queues' => [
                'contexts' => [
                    ConfigProvider::DEFAULT_CONTEXT => [
                        ConfigProvider::CONTEXTS_TYPE_KEY => 'dbal',
                        'connection' => Connection::class,
                    ],
                ],
            ],
        ]);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([ConfigProvider::CONFIG_KEY], [Connection::class])
            ->willReturnOnConsecutiveCalls($config, $this->createMock(Connection::class));

        $factory = new ContextFactory();
        $this->assertInstanceOf(DbalContext::class, $factory->__invoke($container));
    }
}
