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
        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);

        return new LoggerExtension($logger);
    }
}
