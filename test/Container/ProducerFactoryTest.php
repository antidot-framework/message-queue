<?php

declare(strict_types=1);

namespace AntidotTest\Queue\Container;

use Antidot\Queue\Container\ProducerFactory;
use Interop\Queue\Context;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ProducerFactoryTest extends TestCase
{
    public function testItShouldCreateInstancesOfProducer(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(Context::class)
            ->willReturn($this->createMock(Context::class));
        $factory = new ProducerFactory();
        $factory->__invoke($container);
    }
}
