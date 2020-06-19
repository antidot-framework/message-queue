<?php

declare(strict_types=1);

namespace AntidotTest\Queue\Event;

use Antidot\Queue\Event\QueueEvent;
use PHPUnit\Framework\TestCase;

class QueueEventTest extends TestCase
{
    private const PAYLOAD = ['greet' => 'Hello!!'];

    public function testItShouldHaveValidGetters(): void
    {
        $eventClass = new class extends QueueEvent
        {
            public static function occur($payload): self
            {
                $self = new static();
                $self->payload = $payload;
                return $self;
            }
        };
        $event = $eventClass::occur(self::PAYLOAD);
        $this->assertEquals(self::PAYLOAD, $event->payload());
        $this->assertEquals(self::PAYLOAD, $event->jsonSerialize());
        $this->assertEquals(false, $event->isPropagationStopped());



    }
}
