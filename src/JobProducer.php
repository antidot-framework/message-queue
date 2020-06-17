<?php

declare(strict_types=1);

namespace Antidot\Queue;

use Interop\Queue\Context;

class JobProducer implements Producer
{
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /***
     * @param Job $job
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     * @throws \JsonException
     */
    public function enqueue(Job $job): void
    {
        $queue = $this->context->createQueue($job->queueName());
        $message = $this->context->createMessage($job->payload());
        $this->context->createProducer()->send($queue, $message);
    }
}
