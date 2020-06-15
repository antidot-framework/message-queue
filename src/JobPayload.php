<?php

declare(strict_types=1);

namespace Antidot\Queue;

use JsonSerializable;

class JobPayload implements JsonSerializable
{
    private string $type;
    /** @var string|array */
    private $message;

    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return array|string
     */
    public function message()
    {
        return $this->message;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
        ];
    }
}
