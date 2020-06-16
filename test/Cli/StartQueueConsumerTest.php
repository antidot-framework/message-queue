<?php

declare(strict_types=1);


namespace AntidotTest\Queue\Cli;

use Antidot\Queue\Cli\StartQueueConsumer;
use Enqueue\Consumption\QueueConsumerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class StartQueueConsumerTest extends TestCase
{
    private $console;

    public function setUp(): void
    {
        $command = new StartQueueConsumer(
            $this->createMock(QueueConsumerInterface::class),
            $this->createMock(ContainerInterface::class)
        );

        $this->console = new Application();
        $this->console->setAutoExit(false);

        $this->console->add($command);
    }

    public function testItShouldRequireQueueNameToStartRunningTheQueue(): void
    {
        $output = new BufferedOutput();
        $this->console->run(new ArrayInput([StartQueueConsumer::NAME . '']), $output);
        $this->assertStringContainsString('Not enough arguments (missing: "queue_name").', $output->fetch());
    }
}
