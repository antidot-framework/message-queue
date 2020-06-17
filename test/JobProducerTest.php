<?php

declare(strict_types=1);

namespace AntidotTest\Queue;

use Antidot\Queue\Job;
use Antidot\Queue\JobProducer;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Producer as InteropProducer;
use Interop\Queue\Queue;
use PHPUnit\Framework\TestCase;

class JobProducerTest extends TestCase
{
    private const SOME_QUEUE = 'default';
    private const SOME_MESSAGE = 'Hello word!';

    public function testItShouldProduceQueueMessageFromGivenJob(): void
    {
        $job = $this->createMock(Job::class);
        $queue = $this->createMock(Queue::class);
        $message = $this->createMock(Message::class);
        $producer = $this->createMock(InteropProducer::class);
        $context = $this->createMock(Context::class);
        $job->expects($this->once())
            ->method('queueName')
            ->willReturn(self::SOME_QUEUE);
        $job->expects($this->once())
            ->method('payload')
            ->willReturn(self::SOME_MESSAGE);
        $context->expects($this->once())
            ->method('createQueue')
            ->with(self::SOME_QUEUE)
            ->willReturn($queue);
        $context->expects($this->once())
            ->method('createMessage')
            ->with(self::SOME_MESSAGE)
            ->willReturn($message);
        $context->expects($this->once())
            ->method('createProducer')
            ->willReturn($producer);
        $producer->expects($this->once())
            ->method('send')
            ->with($queue, $message);
        $queueProducer = new JobProducer($context);
        $queueProducer->enqueue($job);
    }
}
