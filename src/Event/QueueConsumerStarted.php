<?php

declare(strict_types=1);

namespace Antidot\Queue\Event;

final class QueueConsumerStarted extends QueueEvent
{
    public static function occur(string $queueName): self
    {
        $self = new static();
        $self->payload = ['queue' => $queueName];

        return $self;
    }
}
