<?php

declare(strict_types=1);

namespace Antidot\Queue\Cli;

use Enqueue\Consumption\QueueConsumerInterface;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartQueueConsumer extends Command
{
    public const NAME = 'queue:start';
    private QueueConsumerInterface $consumer;
    private Processor $processor;
    /** @var Context */
    private Context $context;

    public function __construct(QueueConsumerInterface $consumer, Processor $messageProcessor, Context $context)
    {
        $this->consumer = $consumer;
        $this->processor = $messageProcessor;
        $this->context = $context;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Start listening to the given queue name.')
            ->addArgument(
                'queue_name',
                InputArgument::REQUIRED,
                'The queue name we want to consume'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queue = $input->getArgument('queue_name');
        if (false === is_string($queue)) {
            throw new InvalidArgumentException('Argument "queue_name" must be of type string.');
        }
        $this->consumer->bindCallback(
            $queue,
            fn(Message $message) => $this->processor->process($message, $this->context)
        );

        $this->consumer->consume();

        return 0;
    }
}
