<?php

namespace Mhwk\Ouro\Transport\Message;

use Assert\Assertion;

final class OperationResult
{
    const SUCCESS = 0;
    const PREPARE_TIMEOUT = 1;
    const COMMIT_TIMEOUT = 2;
    const FORWARD_TIMEOUT = 3;
    const WRONG_EXPECTED_VERSION = 4;
    const STREAM_DELETED = 5;
    const INVALID_TRANSACTION = 6;
    const ACCESS_DENIED = 7;

    private $result;

    public function __construct(int $result)
    {
        self::assertOperationResult($result);
        $this->result = $result;
    }

    /**
     * @param $result
     */
    private static function assertOperationResult($result)
    {
        Assertion::inArray($result, [
            self::SUCCESS,
            self::PREPARE_TIMEOUT,
            self::COMMIT_TIMEOUT,
            self::FORWARD_TIMEOUT,
            self::WRONG_EXPECTED_VERSION,
            self::STREAM_DELETED,
            self::INVALID_TRANSACTION,
            self::ACCESS_DENIED,
        ], 'Not a valid operation result');
    }
}
