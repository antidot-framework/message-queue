<?php

declare(strict_types=1);

namespace Antidot\Queue;

use function json_encode;

class Job
{
    private string $queueName;
    private JobPayload $payload;

    public function queueName(): string
    {
        return $this->queueName;
    }

    public function payload(): string
    {
        return json_encode($this->payload, true|JSON_THROW_ON_ERROR);
    }
}
