<?php

declare(strict_types=1);

namespace Antidot\Queue\Event;

class QueueConsumerStarted extends QueueEvent
{
    private string $queue;

    public function queue(): string
    {
        return $this->queue;
    }

    protected function assertPayload(array $payload): void
    {
    }
}
