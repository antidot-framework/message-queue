<?php

declare(strict_types=1);

namespace Antidot\Queue;

use Enqueue\Consumption\Result;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use Psr\Container\ContainerInterface;
use Throwable;

class MessageProcessor implements Processor
{
    private const ERROR_MESSAGE_TEMPLATE = 'Error with message: %s. In file %s in line %s. Failing message: %s.';
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(Message $message, Context $context): Result
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
