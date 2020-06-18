<?php

declare(strict_types=1);

namespace Antidot\Queue;

use Assert\Assertion;
use Interop\Queue\Message;
use InvalidArgumentException;
use JsonSerializable;

class JobPayload implements JsonSerializable
{
    public const INVALID_CONTENT_MESSAGE = 'Invalid message content type "%s" given, it must be array or string type.';
    protected string $type;
    /** @var string|array */
    protected $message;

    public static function create(string $messageType, $messageContent): self
    {
        $self = new self();
        $self->type = $messageType;
        $self->assertValidMessageContent($messageContent);
        $self->message = $messageContent;

        return $self;
    }

    public static function createFromMessage(Message $message): self
    {
        $self = new self();
        $payload = json_decode($message->getBody(), true, 10, JSON_THROW_ON_ERROR);
        $self->assertValidMessageData($payload);
        $self->type = $payload['type'];
        $self->message = $payload['message'];

        return $self;
    }

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

    private function assertValidMessageContent($messageContent): void
    {
        if (is_string($messageContent) || is_array($messageContent)) {
            return;
        }

        throw new InvalidArgumentException(sprintf(self::INVALID_CONTENT_MESSAGE, gettype($messageContent)));
    }

    private function assertValidMessageData($payload): void
    {
        Assertion::keyExists($payload, 'type', 'The job payload should have the "type" key.');
        Assertion::string($payload['type'], 'The job payload kay "type" should have a string value.');
        Assertion::keyExists($payload, 'message', 'The job payload should have the "message" key.');
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
        ];
    }
}
