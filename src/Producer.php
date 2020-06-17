<?php

declare(strict_types=1);

namespace Antidot\Queue;

interface Producer
{
    /***
     * @param Job $job
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     * @throws \JsonException
     */
    public function enqueue(Job $job): void;
}
