<?php

namespace Mhwk\Ouro\Transport\Message;

final class DeletePersistentSubscriptionCompleted
{
    /**
     * @var DeletePersistentSubscriptionResult
     */
    private $result;

    /**
     * @var string
     */
    private $reason;

    /**
     * @param DeletePersistentSubscriptionResult $result
     * @param string $reason
     */
    public function __construct(DeletePersistentSubscriptionResult $result, string $reason)
    {
        $this->result = $result;
        $this->reason = $reason;
    }

    /**
     * @return DeletePersistentSubscriptionResult
     */
    public function getResult(): DeletePersistentSubscriptionResult
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
