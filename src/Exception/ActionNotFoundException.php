<?php

declare(strict_types=1);

namespace Antidot\Queue\Exception;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use function sprintf;

class ActionNotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
    public const MESSAGE = 'Action named "%s" does not exist.';

    public static function withId(string $id): self
    {
        return new self(sprintf(self::MESSAGE, $id));
    }
}
