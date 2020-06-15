<?php

declare(strict_types=1);

namespace AntidotTest\Queue\Container;

use Antidot\Queue\Container\ActionContainerFactory;
use Antidot\Queue\Container\Config\ConfigProvider;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ActionContainerFactoryTest extends TestCase
{
    private const CONFIG_KEY = 'config';
    private const INVALID_CONFIG = [
        'queues' => [
            'contexts' => [
            ],
        ],
    ];
    private const VALID_CONFIG = [
        'queues' => [
            'contexts' => [
                ConfigProvider::DEFAULT_CONTEXT => [
                    'message_types' => [
                        'some_message_type' => 'some_service',
                        'some_other_message_type' => 'some_other_service',
                        'and_some_message_type' => 'and_some_service',
                    ]
                ]
            ],
        ],
    ];

    public function testItShouldThrowExceptionWhenNoContextAreDefined(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(ConfigProvider::INVALID_CONTEXT_MESSAGE, ConfigProvider::DEFAULT_CONTEXT)
        );
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(self::CONFIG_KEY)
            ->willReturn(self::INVALID_CONFIG);
        $factory = new ActionContainerFactory();
        $factory->__invoke($container);
    }

    public function testItShouldCreateAnActionContainerInstances(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(self::CONFIG_KEY)
            ->willReturn(self::VALID_CONFIG);
        $factory = new ActionContainerFactory();
        $factory->__invoke($container);
    }
}
