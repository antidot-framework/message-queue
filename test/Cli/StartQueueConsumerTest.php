<?php

declare(strict_types=1);


namespace AntidotTest\Queue\Cli;

use Antidot\Queue\Cli\StartQueueConsumer;
use Antidot\Queue\MessageProcessor;
use Enqueue\Consumption\QueueConsumerInterface;
use Interop\Queue\Context;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class StartQueueConsumerTest extends TestCase
{
    private $console;

    protected function setUp(): void
    {
        $command = new StartQueueConsumer(
            $this->createMock(QueueConsumerInterface::class),
            $this->createMock(MessageProcessor::class),
            $this->createMock(Context::class),
            $this->createMock(EventDispatcherInterface::class)
        );

        $this->console = new Application();
        $this->console->setAutoExit(false);

        $this->console->add($command);
    }

    public function testItShouldRequireQueueNameToStartRunningTheQueue(): void
    {
        $output = new BufferedOutput();
        $this->console->run(new ArrayInput(['command' => StartQueueConsumer::NAME]), $output);
        $this->assertStringContainsString('Not enough arguments (missing: "queue_name").', $output->fetch());
    }

    public function testItShouldRequireQueueNameToBeAStringToStartRunningTheQueue(): void
    {
        $output = new BufferedOutput();
        $this->console->run(new ArrayInput(['command' => StartQueueConsumer::NAME, 'queue_name' => ['default', 'true']]), $output);
        $this->assertStringContainsString(StartQueueConsumer::INVALID_NAME_MESSAGE, $output->fetch());
    }

    public function testItShouldStartListeningToTheGivenQueueName(): void
    {
        $output = new BufferedOutput();
        $result = $this->console->run(new ArrayInput(['command' => StartQueueConsumer::NAME, 'queue_name' => 'default']), $output);
        $this->assertSame(0, $result);
    }
}
