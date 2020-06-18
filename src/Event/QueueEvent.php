<?php

declare(strict_types=1);

namespace Antidot\Queue\Event;

use JsonSerializable;
use Psr\EventDispatcher\StoppableEventInterface;

abstract class QueueEvent implements StoppableEventInterface, JsonSerializable
{
    protected array $payload;

    public static function occur(array $payload): self
    {
        $self = new static();
        $self->assertPayload($payload);
        $self->payload = $payload;

        return $self;
    }

    abstract protected function assertPayload(array $payload): void;

    public function payload(): array
    {
        return $this->payload;
    }

    public function isPropagationStopped(): bool
    {
        return false;
    }

    public function jsonSerialize(): array
    {
        return $this->payload;
    }
}
