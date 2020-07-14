<?php

declare(strict_types=1);

namespace Antidot\Queue;

use Antidot\Queue\Exception\ActionContainerException;
use Antidot\Queue\Exception\ActionNotFoundException;
use Psr\Container\ContainerInterface;
use Throwable;
use function array_key_exists;

class ActionContainer implements ContainerInterface
{
    /** @var array<callable>  */
    private array $actions;

    /**
     * @param array<callable> $actions
     */
    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    public function get($id): callable
    {
        if (false === $this->has($id)) {
            throw ActionNotFoundException::withId($id);
        }

        try {
            $action = $this->actions[$id]();
        } catch (Throwable $exception) {
            throw ActionContainerException::withIdAndPreviousException($id, $exception);
        }

        return $action;
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->actions);
    }
}
