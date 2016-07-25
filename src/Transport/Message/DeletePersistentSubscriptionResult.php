<?php

namespace Mhwk\Ouro\Transport\Message;

use Assert\Assertion;

final class DeletePersistentSubscriptionResult
{
    const SUCCESS = 0;
    const DOES_NOT_EXIST = 1;
    const FAIL = 2;
    const ACCESS_DENIED = 3;

    /**
     * @param int $result
     */
    public function __construct(int $result)
    {
        self::assertDeletePersistentSubscriptionResult($result);
        $this->result = $result;
    }

    /**
     * @param int $result
     */
    private static function assertDeletePersistentSubscriptionResult(int $result)
    {
        Assertion::inArray($result, [
            self::SUCCESS,
            self::DOES_NOT_EXIST,
            self::FAIL,
            self::ACCESS_DENIED,
        ], 'Not a valid result');
    }

    /**
     * @return DeletePersistentSubscriptionResult
     */
    public static function success()
    {
        return new self(self::SUCCESS);
    }

    /**
     * @return DeletePersistentSubscriptionResult
     */
    public static function doesNotExist()
    {
        return new self(self::DOES_NOT_EXIST);
    }

    /**
     * @return DeletePersistentSubscriptionResult
     */
    public static function fail()
    {
        return new self(self::FAIL);
    }

    /**
     * @return DeletePersistentSubscriptionResult
     */
    public static function accessDenied()
    {
        return new self(self::ACCESS_DENIED);
    }
}
