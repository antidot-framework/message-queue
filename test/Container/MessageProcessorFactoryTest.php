<?php

declare(strict_types=1);

namespace AntidotTest\Queue\Container;

use Antidot\Queue\ActionContainer;
use Antidot\Queue\Container\MessageProcessorFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class MessageProcessorFactoryTest extends TestCase
{
    public function testItShouldCreateMessageProcessorInstances(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ActionContainer::class)
            ->willReturn($this->createMock(ActionContainer::class));
        $factory = new MessageProcessorFactory();
        $factory->__invoke($container);
    }
}
