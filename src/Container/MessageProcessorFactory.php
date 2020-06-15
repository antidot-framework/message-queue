<?php

declare(strict_types=1);

namespace Antidot\Queue\Container;

use Antidot\Queue\ActionContainer;
use Antidot\Queue\MessageProcessor;
use Psr\Container\ContainerInterface;

class MessageProcessorFactory
{
    public function __invoke(ContainerInterface $container): MessageProcessor
    {
        $actionContainer = $container->get(ActionContainer::class);

        return new MessageProcessor($actionContainer);
    }
}
