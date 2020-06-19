<?php

declare(strict_types=1);

namespace AntidotTest\Queue;

use Antidot\Queue\ActionContainer;
use Antidot\Queue\Event\MessageProcessed;
use Antidot\Queue\Event\MessageReceived;
use Antidot\Queue\JobPayload;
use Antidot\Queue\MessageProcessor;
use Exception;
use Interop\Queue\Context;
use Interop\Queue\Message;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class MessageProcessorTest extends TestCase
{
    private const SOME_MESSAGE_TYPE = 'some_message_type';
    private const SOME_MESSAGE = 'Hello World';
    private const ACTION_EXCEPTION_MESsAGE = 'Action Error Message';
    private const JSON_MESSAGE = '{"type":"' . self::SOME_MESSAGE_TYPE . '","message":"' . self::SOME_MESSAGE . '"}';
    private const INVALID_FORMAT_MESSAGE = '{"some":"' . self::SOME_MESSAGE_TYPE . '","other":"' . self::SOME_MESSAGE . '"}';
    private $context;
    private $message;
    private $actionContainer;
    private $eventDispatcher;

    public function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->message = $this->createMock(Message::class);
        $this->actionContainer = $this->createMock(ActionContainer::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->isInstanceOf(MessageReceived::class)],
                [$this->isInstanceOf(MessageProcessed::class)]
            );
    }

    public function testItShouldOmitGivenQueueJobsWhenNoActionConfigured(): void
    {
        $this->message->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn(self::JSON_MESSAGE);
        $this->actionContainer->expects($this->once())
            ->method('has')
            ->with(self::SOME_MESSAGE_TYPE)
            ->willReturn(false);
        $messageProcessor = new MessageProcessor($this->actionContainer, $this->eventDispatcher);
        $result = $messageProcessor->process($this->message, $this->context);
        $this->assertStringContainsString(MessageProcessor::ACK, (string)$result);
    }

    public function testItShouldConsumeGivenQueueJobsInTheConfiguredAction(): void
    {
        $this->message->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn(self::JSON_MESSAGE);
        $this->actionContainer->expects($this->once())
            ->method('has')
            ->with(self::SOME_MESSAGE_TYPE)
            ->willReturn(true);
        $this->actionContainer->expects($this->once())
            ->method('get')
            ->with(self::SOME_MESSAGE_TYPE)
            ->willReturn(static fn(JobPayload $payload) => $payload->message());
        $messageProcessor = new MessageProcessor($this->actionContainer, $this->eventDispatcher);
        $result = $messageProcessor->process($this->message, $this->context);
        $this->assertStringContainsString(MessageProcessor::ACK, (string)$result);
    }

    public function testItShouldRejectMessageWithInvalidMessage(): void
    {
        $this->message->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn(self::INVALID_FORMAT_MESSAGE);
        $messageProcessor = new MessageProcessor($this->actionContainer, $this->eventDispatcher);
        $result = $messageProcessor->process($this->message, $this->context);
        $this->assertStringContainsString(MessageProcessor::REJECT, (string)$result);
    }

    public function testItShouldRejectMessageWhenActionExecutionFails(): void
    {
        $this->message->expects($this->atLeastOnce())
            ->method('getBody')
            ->willReturn(self::JSON_MESSAGE);
        $this->actionContainer->expects($this->once())
            ->method('has')
            ->with(self::SOME_MESSAGE_TYPE)
            ->willReturn(true);
        $this->actionContainer->expects($this->once())
            ->method('get')
            ->with(self::SOME_MESSAGE_TYPE)
            ->willReturn(
                static function () {
                    throw new Exception(self::ACTION_EXCEPTION_MESsAGE);
                }
            );
        $messageProcessor = new MessageProcessor($this->actionContainer, $this->eventDispatcher);
        $result = $messageProcessor->process($this->message, $this->context);
        $this->assertStringContainsString(MessageProcessor::REJECT, (string)$result);
        $this->assertStringContainsString(self::ACTION_EXCEPTION_MESsAGE, $result->getReason());
    }
}
