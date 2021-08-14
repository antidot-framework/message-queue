<?php

declare(strict_types=1);

namespace Antidot\Queue\Cli;

use Antidot\Queue\Event\QueueConsumerStarted;
use Antidot\Queue\MessageProcessor;
use Enqueue\Consumption\QueueConsumerInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartQueueConsumer extends Command
{
    public const NAME = 'queue:start';
    public const INVALID_NAME_MESSAGE = 'Argument "queue_name" must be of type string.';
    private QueueConsumerInterface $consumer;
    private Processor $processor;
    private Context $context;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        QueueConsumerInterface $consumer,
        Processor $messageProcessor,
        Context $context,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->consumer = $consumer;
        $this->processor = $messageProcessor;
        $this->context = $context;
        $this->eventDispatcher = $eventDispatcher;
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
            throw new InvalidArgumentException(self::INVALID_NAME_MESSAGE);
        }
        $this->eventDispatcher->dispatch(QueueConsumerStarted::occur($queue));
        /** @var MessageProcessor $processor */
        $processor = $this->processor;
        $context = $this->context;
        $callback = static fn(Message $message): Result => $processor->process($message, $context);
        $this->consumer->bindCallback($queue, $callback);
        $this->consumer->consume();

        return 0;
    }
}
