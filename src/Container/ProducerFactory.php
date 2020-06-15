<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\Producer;
use Interop\Queue\Context;
use Psr\Container\ContainerInterface;

class ProducerFactory
{
    public function __invoke(ContainerInterface $container): Producer
    {
        $context = $container->get(Context::class);

        return new Producer($context);
    }
}
