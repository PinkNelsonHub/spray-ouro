<?php

namespace Mhwk\Ouro\Transport\Message;

final class SubscriptionDropReason
{
    const USER_INITIATED = 'USER_INITIATED';
    const NOT_AUTHENTICATED = 'NOT_AUTHENTICATED';
    const ACCESS_DENIED = 'ACCESS_DENIED';
    const SUBSCRIBING_ERROR = 'SUBSCRIBING_ERROR';
    const SERVER_ERROR = 'SERVER_ERROR';
    const CONNECTION_CLOSED = 'CONNECTION_CLOSED';
    const CATCH_UP_ERROR = 'CATCH_UP_ERROR';
    const PROCESSING_QUEUE_OVERFLOW = 'PROCESSING_QUEUE_OVERFLOW';
    const EVENT_HANDLER_EXCEPTION = 'EVENT_HANDLER_EXCEPTION';
    const MAX_SUBSCRIBERS_REACHED = 'MAX_SUBSCRIBERS_REACHED';
    const PERSISTENT_SUBSCRIPTION_DELETED = 'PERSISTENT_SUBSCRIPTION_DELETED';
    const UNKNOWN = 'UNKNOWN';
    const NOT_FOUND = 'NOT_FOUND';

    /**
     * @var string
     */
    private $reason;

    /**
     * @param string $reason
     */
    public function __construct(string $reason)
    {
        self::assertSubscriptionDropReason($reason);
        $this->reason = $reason;
    }

    /**
     * @param string $reason
     */
    static function assertSubscriptionDropReason(string $reason)
    {
        Assertion::inArray($reason, [
            self::USER_INITIATED,
            self::NOT_AUTHENTICATED,
            self::ACCESS_DENIED,
            self::SUBSCRIBING_ERROR,
            self::SERVER_ERROR,
            self::CONNECTION_CLOSED,
            self::CATCH_UP_ERROR,
            self::PROCESSING_QUEUE_OVERFLOW,
            self::EVENT_HANDLER_EXCEPTION,
            self::MAX_SUBSCRIBERS_REACHED,
            self::PERSISTENT_SUBSCRIPTION_DELETED,
            self::UNKNOWN,
            self::NOT_FOUND,
        ], sprintf('\'%s\' is not a valid subscription drop reason', $reason));
    }
}
