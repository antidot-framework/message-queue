<?php

declare(strict_types=1);

namespace Antidot\Queue\Event;

class MessageProcessed extends QueueEvent
{
    protected function assertPayload(array $payload): void
    {
    }
}
