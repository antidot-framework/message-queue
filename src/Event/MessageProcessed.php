<?php

declare(strict_types=1);

namespace Antidot\Queue\Event;

use Enqueue\Consumption\Result;

class MessageProcessed extends QueueEvent
{
    public static function occur(Result $result): self
    {
        $self = new static();
        $self->payload = [
            'status' => $result->getStatus(),
            'reason' => $result->getReason(),
            'reply' => $result->getReply(),
        ];

        return $self;
    }
}
