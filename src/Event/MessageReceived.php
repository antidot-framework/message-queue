<?php

declare(strict_types=1);

namespace Antidot\Queue\Event;

use Interop\Queue\Message;

class MessageReceived extends QueueEvent
{
    private Message $message;

    public function message(): Message
    {
        return $this->message;
    }

    protected function assertPayload(array $payload): void
    {
    }
}
