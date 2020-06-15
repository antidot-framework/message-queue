<?php

declare(strict_types=1);

namespace AntidotTest\Queue;

use Antidot\Queue\JobPayload;
use Antidot\Queue\MessageProcessor;
use PHPUnit\Framework\TestCase;

class MessageProcessorTest extends TestCase
{
    private const SOME_MESSAGE_TYPE = 'some_message_type';
    private const SOME_MESSAGE = 'Hello World';

    public function testItShouldConsumeGivenQueueJobs(): void
    {
        $actionContainer = $this->createMock(\Antidot\Queue\ActionContainer::class);
        $actionContainer->expects($this->once())
            ->method('get')
            ->with(self::SOME_MESSAGE_TYPE)
            ->willReturn(static fn(JobPayload $payload) => $payload->message());
        $payload = $this->createMock(JobPayload::class);
        $payload->expects($this->once())
            ->method('type')
            ->willReturn(self::SOME_MESSAGE_TYPE);
        $payload->expects($this->once())
            ->method('message')
            ->willReturn(self::SOME_MESSAGE);
        $messageProcessor = new MessageProcessor($actionContainer);
        $messageProcessor->process($payload);
    }
}
