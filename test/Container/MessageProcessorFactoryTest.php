<?php

declare(strict_types=1);

namespace AntidotTest\Queue\Container;

use Antidot\Queue\ActionContainer;
use Antidot\Queue\Container\Config\ConfigProvider;
use Antidot\Queue\Container\MessageProcessorFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class MessageProcessorFactoryTest extends TestCase
{
    public function testItShouldCreateMessageProcessorInstances(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([ConfigProvider::CONFIG_KEY], [ConfigProvider::DEFAULT_CONTAINER_SERVICE])
            ->willReturnOnConsecutiveCalls(
                ConfigProvider::DEFAULT_CONFIG,
                $this->createMock(ActionContainer::class)
            );
        $factory = new MessageProcessorFactory();
        $factory->__invoke($container);
    }
}
