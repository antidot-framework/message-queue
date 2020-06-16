<?php

declare(strict_types=1);


namespace AntidotTest\Queue;

use Antidot\Queue\JobPayload;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class JobPayloadTest extends TestCase
{
    private const MESSAGE_TYPE = 'some_type';
    private const MESSAGE_CONTENT = 'some_content';
    private const ARRAY_MESSAGE_CONTENT = ['some_content'];

    public function testIsShouldBeCreatedWithStringMessage(): void
    {
        $jobPayload = JobPayload::create(self::MESSAGE_TYPE, self::MESSAGE_CONTENT);
        $this->assertEquals(self::MESSAGE_TYPE, $jobPayload->type());
        $this->assertEquals(self::MESSAGE_CONTENT, $jobPayload->message());
    }

    public function testIsShouldBeCreatedWithArrayMessage(): void
    {
        $jobPayload = JobPayload::create(self::MESSAGE_TYPE, self::ARRAY_MESSAGE_CONTENT);
        $this->assertEquals(self::MESSAGE_TYPE, $jobPayload->type());
        $this->assertEquals(self::ARRAY_MESSAGE_CONTENT, $jobPayload->message());
    }

    public function testIsShouldThrowExceptionWithNonArrayOrStringMessage(): void
    {
        $object = new \SplStack();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(JobPayload::INVALID_CONTENT_MESSAGE, gettype($object)));
        JobPayload::create(self::MESSAGE_TYPE, $object);
    }
}
