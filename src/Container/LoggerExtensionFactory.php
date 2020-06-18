<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Enqueue\Consumption\Extension\LoggerExtension;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggerExtensionFactory
{
    public function __invoke(ContainerInterface $container): LoggerExtension
    {
        return new LoggerExtension($container->get(LoggerInterface::class));
    }
}
