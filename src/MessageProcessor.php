<?php

declare(strict_types=1);

namespace Antidot\Queue;

use Antidot\Queue\Event\MessageProcessed;
use Antidot\Queue\Event\MessageReceived;
use Enqueue\Consumption\Result;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

class MessageProcessor implements Processor
{
    private const ERROR_MESSAGE_TEMPLATE = 'Error with message: %s. In file %s in line %s. Failing message: %s.';
    private ContainerInterface $container;
    private EventDispatcherInterface $dispatcher;

    public function __construct(ContainerInterface $container, EventDispatcherInterface $dispatcher)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
    }

    public function process(Message $message, Context $context): Result
    {
        $this->dispatcher->dispatch(MessageReceived::occur(['message' => $message]));
        $result = $this->processMessage($message);
        $this->dispatcher->dispatch(MessageProcessed::occur(['result' => $result]));

        return $result;
    }
    public function processMessage(Message $message): Result
    {
        $jobPayload = null;
        try {
            $jobPayload = JobPayload::createFromMessage($message);
            if ($this->container->has($jobPayload->type())) {
                $action = $this->container->get($jobPayload->type());
                $action($jobPayload);
                return Result::ack(sprintf('Message type "%s" routed to action.', $jobPayload->type()));
            }

            return Result::ack(
                sprintf('No action matched for message type "%s". Message omitted.', $jobPayload->type())
            );
        } catch (Throwable $exception) {
            $errorMessage = sprintf(
                self::ERROR_MESSAGE_TEMPLATE,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                json_encode($message, JSON_THROW_ON_ERROR)
            );

            if (null === $jobPayload) {
                return Result::reject(sprintf('Invalid message format given and caused an error: %s', $errorMessage));
            }

            return Result::reject(
                sprintf('Error occurred processing "%s" message: %s', $jobPayload->type(), $errorMessage)
            );
        }
    }
}
