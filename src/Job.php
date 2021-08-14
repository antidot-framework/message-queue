<?php

declare(strict_types=1);

namespace Antidot\Queue;

use InvalidArgumentException;
use function json_encode;

final class Job
{
    private string $queueName;
    private JobPayload $payload;

    private function __construct(string $queueName, JobPayload $payload)
    {
        $this->queueName = $queueName;
        $this->payload = $payload;
    }

    /**
     * @param mixed $messageContent
     */
    public static function create(string $queueName, string $messageType, $messageContent): self
    {
        if (false === is_array($messageContent) && false === is_string($messageContent)) {
            throw new InvalidArgumentException('$messageContent must be a string or an array.');
        }

        /** @var array<string, mixed>|string $messageContent */
        return new self($queueName, JobPayload::create($messageType, $messageContent));
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
