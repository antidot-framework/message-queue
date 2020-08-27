<?php

declare(strict_types=1);


namespace AntidotTest\Queue\Container;

use Antidot\Queue\Container\LoggerExtensionFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggerExtensionFactoryTest extends TestCase
{
    public function testItShouldCreateInstancesOfLoggerExtension(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(LoggerInterface::class)
            ->willReturn($this->createMock(LoggerInterface::class));
        $factory = new LoggerExtensionFactory();
        $factory->__invoke($container);
    }
}
