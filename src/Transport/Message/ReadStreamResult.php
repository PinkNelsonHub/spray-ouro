<?php

namespace Spray\Ouro\Transport\Message;

use Assert\Assertion;

final class ReadStreamResult
{
    const SUCCESS = 0;
    const NO_STREAM = 1;
    const STREAM_DELETED = 2;
    const NOT_MODIFIED = 3;
    const ERROR = 4;
    const ACCESS_DENIED = 5;

    /**
     * @var int
     */
    private $result;

    /**
     * @param int $result
     */
    public function __construct(int $result)
    {
        self::assertReadStreamResult($result);
        $this->result = $result;
    }

    private static function assertReadStreamResult($result)
    {
        Assertion::inArray($result, [
            self::SUCCESS,
            self::NO_STREAM,
            self::STREAM_DELETED,
            self::NOT_MODIFIED,
            self::ERROR,
            self::ACCESS_DENIED,
        ], 'Not a ReadStreamResult');
    }
}
