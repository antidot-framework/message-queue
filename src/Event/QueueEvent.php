<?php

declare(strict_types=1);

namespace Antidot\Queue\Event;

use JsonSerializable;
use Psr\EventDispatcher\StoppableEventInterface;

abstract class QueueEvent implements StoppableEventInterface, JsonSerializable
{
    protected array $payload = [];

    public function __construct()
    {
    }

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
