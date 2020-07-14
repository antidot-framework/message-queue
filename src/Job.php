<?php

declare(strict_types=1);

namespace Antidot\Queue;

use function json_encode;

class Job
{
    private string $queueName;
    private JobPayload $payload;

    private function __construct()
    {
    }

    /**
     * @param mixed $messageContent
     */
    public static function create(string $queueName, string $messageType, $messageContent): self
    {
        $self = new self();
        $self->queueName = $queueName;
        $self->payload = JobPayload::create($messageType, $messageContent);

        return $self;
    }

    public function queueName(): string
    {
        return $this->queueName;
    }

    public function payload(): string
    {
        return json_encode($this->payload, JSON_THROW_ON_ERROR);
    }
}
