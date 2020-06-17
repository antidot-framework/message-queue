<?php

declare(strict_types=1);


namespace AntidotTest\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Antidot\Queue\Container\StartQueueConsumerFactory;
use Enqueue\Consumption\QueueConsumerInterface;
use Interop\Queue\Context;
use Interop\Queue\Processor;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class StartQueueConsumerFactoryTest extends TestCase
{
    public function testItShouldCreateInstancesOfStartConsumerFactory(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(4))
            ->method('get')
            ->withConsecutive(
                [ConfigProvider::CONFIG_KEY],
                [QueueConsumerInterface::class],
                [Processor::class],
                [ConfigProvider::DEFAULT_CONTEXT_SERVICE]
            )
            ->willReturnOnConsecutiveCalls(
                ConfigProvider::DEFAULT_CONFIG,
                $this->createMock(QueueConsumerInterface::class),
                $this->createMock(Processor::class),
                $this->createMock(Context::class)
            );

        $factory = new StartQueueConsumerFactory();
        $factory->__invoke($container);
    }
}
