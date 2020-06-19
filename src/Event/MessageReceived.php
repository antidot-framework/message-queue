<?php

declare(strict_types=1);

namespace Antidot\Queue\Event;

use Interop\Queue\Message;

class MessageReceived extends QueueEvent
{
    public static function occur(Message $message): self
    {
        $self = new static();
        $self->payload = [
            'message_id' => $message->getMessageId(),
            'headers' => $message->getHeaders(),
            'body' => $message->getBody(),
            'properties' => $message->getProperties(),
            'reply_to' => $message->getReplyTo(),
            'timestamp' => $message->getTimestamp(),
        ];

        return $self;
    }
}
