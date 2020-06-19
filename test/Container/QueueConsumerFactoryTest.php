<?php

declare(strict_types=1);

namespace AntidotTest\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Antidot\Queue\Container\QueueConsumerFactory;
use Enqueue\Consumption\Extension\LogExtension;
use Enqueue\Consumption\Extension\LoggerExtension;
use Enqueue\Consumption\Extension\SignalExtension;
use Interop\Queue\Context;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class QueueConsumerFactoryTest extends TestCase
{
    public function testItShouldCreateInstancesOfConsumerInterface(): void
    {

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(6))
            ->method('get')
            ->withConsecutive(
                [ConfigProvider::CONFIG_KEY],
                [LoggerExtension::class],
                [SignalExtension::class],
                [LogExtension::class],
                [ConfigProvider::DEFAULT_CONTEXT_SERVICE],
                [LoggerInterface::class]
            )
            ->willReturnOnConsecutiveCalls(
                ConfigProvider::DEFAULT_CONFIG,
                $this->createMock(LoggerExtension::class),
                $this->createMock(SignalExtension::class),
                $this->createMock(LogExtension::class),
                $this->createMock(Context::class),
                $this->createMock(LoggerInterface::class)
            );

        $factory = new QueueConsumerFactory();
        $factory->__invoke($container);
    }
}
