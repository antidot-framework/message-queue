<?php

declare(strict_types=1);

namespace AntidotTest\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Enqueue\Consumption\Extension\LoggerExtension;
use Interop\Queue\Context;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class QueueConsumerFactoryTest extends TestCase
{
    public function testItShouldCreateInstancesOfConsumerInterface(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(4))
            ->method('get')
            ->withConsecutive(
                [ConfigProvider::CONFIG_KEY],
                [LoggerExtension::class],
                [ConfigProvider::DEFAULT_CONTEXT_SERVICE],
                [LoggerInterface::class]
            )
            ->willReturnOnConsecutiveCalls(
                ConfigProvider::DEFAULT_CONFIG,
                $this->createMock(LoggerExtension::class),
                $this->createMock(Context::class),
                $this->createMock(LoggerInterface::class)
            );

        $factory = new \Antidot\Queue\Container\QueueConsumerFactory();
        $factory->__invoke($container);
    }
}
