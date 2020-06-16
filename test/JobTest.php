<?php

declare(strict_types=1);

namespace AntidotTest\Queue;

use Antidot\Queue\Job;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    private const SOME_QUEUE_NAME = 'some_queue';
    private const MESSAGE_TYPE = 'some_type';
    private const MESSAGE_CONTENT = 'Some message';
    private const MESSAGE_ARRAY_CONTENT = [
        'hello' => self::MESSAGE_CONTENT
    ];
    private const PAYLOAD = '{"type":"' . self::MESSAGE_TYPE . '","message":"' . self::MESSAGE_CONTENT . '"}';
    private const ARRAY_PAYLOAD = '{"type":"' . self::MESSAGE_TYPE . '","message":{"hello":"' . self::MESSAGE_CONTENT . '"}}';

    public function testItShouldBeCreatedWithQueueNameMessageTypeAndMessage(): void
    {
        $job = Job::create(self::SOME_QUEUE_NAME, self::MESSAGE_TYPE, self::MESSAGE_CONTENT);
        $this->assertEquals(self::SOME_QUEUE_NAME, $job->queueName());
        $this->assertEquals(self::PAYLOAD, $job->payload());
    }

    public function testItShouldBeCreatedWithQueueNameMessageTypeAndArrayMessage(): void
    {
        $job = Job::create(self::SOME_QUEUE_NAME, self::MESSAGE_TYPE, self::MESSAGE_ARRAY_CONTENT);
        $this->assertEquals(self::SOME_QUEUE_NAME, $job->queueName());
        $this->assertEquals(self::ARRAY_PAYLOAD, $job->payload());
    }
}
