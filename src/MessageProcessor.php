<?php

declare(strict_types=1);

namespace Antidot\Queue;

use Psr\Container\ContainerInterface;

class MessageProcessor
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(JobPayload $payload): void
    {
        $action = $this->container->get($payload->type());
        $action($payload);
    }
}
