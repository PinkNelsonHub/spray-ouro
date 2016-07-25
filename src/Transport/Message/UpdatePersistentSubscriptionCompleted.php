<?php

namespace Mhwk\Ouro\Transport\Message;

final class UpdatePersistentSubscriptionCompleted
{
    /**
     * @var UpdatePersistentSubscriptionResult
     */
    private $result;
    /**
     * @var string
     */
    private $reason;

    /**
     * @param UpdatePersistentSubscriptionResult $result
     * @param string $reason
     */
    public function __construct(UpdatePersistentSubscriptionResult $result, string $reason)
    {
        $this->result = $result;
        $this->reason = $reason;
    }

    /**
     * @return UpdatePersistentSubscriptionResult
     */
    public function getResult(): UpdatePersistentSubscriptionResult
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }
}
