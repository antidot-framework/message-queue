<?php

declare(strict_types=1);

namespace Antidot\Queue\Cli;

use Antidot\Queue\JobPayload;
use Enqueue\Consumption\QueueConsumerInterface;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class StartQueueConsumer extends Command
{
    public const NAME = 'queue:start';
    private const ERROR_MESSAGE_TEMPLATE = 'Error with message: %s. In file %s in line %s. Failing message: %s.';
    private QueueConsumerInterface $consumer;
    private ContainerInterface $actionContainer;

    public function __construct(QueueConsumerInterface $consumer, ContainerInterface $actionContainer)
    {
        $this->consumer = $consumer;
        $this->actionContainer = $actionContainer;
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
        $container = $this->actionContainer;
        $this->consumer->bindCallback(
            $queue,
            static function (Message $message) use ($container) {
                try {
                    $jobPayload = JobPayload::createFromMessage($message);
                    $action = $container->get($jobPayload->type());
                    $action($jobPayload);

                    return Processor::ACK;
                } catch (Throwable $exception) {
                    trigger_error(
                        sprintf(
                            self::ERROR_MESSAGE_TEMPLATE,
                            $exception->getMessage(),
                            $exception->getFile(),
                            $exception->getLine(),
                            json_encode($message, JSON_THROW_ON_ERROR)
                        ),
                        E_USER_WARNING
                    );

                    return Processor::REJECT;
                }
            }
        );

        $this->consumer->consume();

        return 0;
    }
}
