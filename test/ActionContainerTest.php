<?php

declare(strict_types=1);

namespace AntidotTest\Queue;

use Antidot\Queue\ActionContainer;
use Antidot\Queue\Exception\ActionContainerException;
use Antidot\Queue\Exception\ActionNotFoundException;
use Exception;
use PHPUnit\Framework\TestCase;
use function sprintf;

class ActionContainerTest extends TestCase
{
    private const SOME_ACTION = 'some_action';
    private const ACTION_INSTANCE_VALUE = ':rocket:';

    public function testItShouldThrowAnExceptionWhenGivenActionIdNotFound(): void
    {
        $actions = [];
        $this->expectException(ActionNotFoundException::class);
        $this->expectExceptionMessage(sprintf(ActionNotFoundException::MESSAGE, self::SOME_ACTION));
        $actionContainer = new ActionContainer($actions);
        $actionContainer->get(self::SOME_ACTION);
    }

    public function testItShouldThrowAnExceptionWhenGivenActionFailsOnInstantiation(): void
    {
        $actions = [
            self::SOME_ACTION => static function () {
                throw new Exception('Some Error Occurred.');
            }
        ];
        $this->expectException(ActionContainerException::class);
        $this->expectExceptionMessage(sprintf(ActionContainerException::MESSAGE, self::SOME_ACTION));
        $actionContainer = new ActionContainer($actions);
        $actionContainer->get(self::SOME_ACTION);
    }

    public function testItShouldGetActionInstance(): void
    {
        $actions = [
            self::SOME_ACTION => static fn () => static fn () => self::ACTION_INSTANCE_VALUE,
        ];
        $actionContainer = new ActionContainer($actions);
        $callable = $actionContainer->get(self::SOME_ACTION);
        $this->assertSame(self::ACTION_INSTANCE_VALUE, $callable());
    }
}
