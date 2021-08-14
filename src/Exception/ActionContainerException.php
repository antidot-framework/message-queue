<?php

declare(strict_types=1);

namespace Antidot\Queue\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;
use Throwable;

use function sprintf;

class ActionContainerException extends RuntimeException implements ContainerExceptionInterface
{
    public const MESSAGE = 'Error occurred instantiating "%s" class, see previous exception for more details.';
    public const MESSAGE_INVALID_CALLABLE = 'Error occurred instantiating "%s" class, it must be callable.';

    public static function withIdAndPreviousException(string $id, Throwable $exception): self
    {
        return new self(sprintf(self::MESSAGE, $id), 0, $exception);
    }

    public static function withIdAndInvalidCallable(string $id): self
    {
        return new self(sprintf(self::MESSAGE_INVALID_CALLABLE, $id));
    }
}
