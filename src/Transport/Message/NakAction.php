<?php

namespace Spray\Ouro\Transport\Message;

use Assert\Assertion;

final class NakAction
{
    const UNKNOWN = 0;
    const PARK = 1;
    const RETRY = 2;
    const SKIP = 3;
    const STOP = 4;

    /**
     * @var int
     */
    private $action;

    /**
     * @param int $action
     */
    public function __construct(int $action)
    {
        self::assertNakAction($action);
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        switch ($this->action) {
            case self::UNKNOWN:
                return 'Unknown';
            case self::PARK:
                return 'Park';
            case self::RETRY:
                return 'Retry';
            case self::SKIP:
                return 'Skip';
            case self::STOP:
                return 'Stop';
        }
    }

    /**
     * @param int $action
     */
    private static function assertNakAction(int $action)
    {
        Assertion::inArray($action, [
            self::UNKNOWN,
            self::PARK,
            self::RETRY,
            self::SKIP,
            self::STOP,
        ], 'Not a NakAction');
    }

    /**
     * @return NakAction
     */
    public static function unknown()
    {
        return new self(self::UNKNOWN);
    }

    /**
     * @return NakAction
     */
    public static function park()
    {
        return new self(self::PARK);
    }

    /**
     * @return NakAction
     */
    public static function retry()
    {
        return new self(self::RETRY);
    }

    /**
     * @return NakAction
     */
    public static function skip()
    {
        return new self(self::SKIP);
    }

    /**
     * @return NakAction
     */
    public static function stop()
    {
        return new self(self::STOP);
    }
}
