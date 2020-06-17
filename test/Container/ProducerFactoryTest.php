<?php

declare(strict_types=1);

namespace AntidotTest\Queue\Container;

use Antidot\Queue\Container\Config\ConfigProvider;
use Antidot\Queue\Container\ProducerFactory;
use Interop\Queue\Context;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ProducerFactoryTest extends TestCase
{
    public function testItShouldCreateInstancesOfProducer(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive([ConfigProvider::CONFIG_KEY], [ConfigProvider::DEFAULT_CONTEXT_TYPE . '.context'])
            ->willReturnOnConsecutiveCalls(ConfigProvider::DEFAULT_CONFIG, $this->createMock(Context::class));
        $factory = new ProducerFactory();
        $factory->__invoke($container);
    }
}
