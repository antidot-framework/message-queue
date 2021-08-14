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
        $job = Job::create(self::SOME_QUEUE, 'some_type',self::SOME_MESSAGE);
        $queue = $this->createMock(Queue::class);
        $message = $this->createMock(Message::class);
        $producer = $this->createMock(InteropProducer::class);
        $context = $this->createMock(Context::class);
        $context->expects($this->once())
            ->method('createQueue')
            ->with(self::SOME_QUEUE)
            ->willReturn($queue);
        $context->expects($this->once())
            ->method('createMessage')
            ->with($job->payload())
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
