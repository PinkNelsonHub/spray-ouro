<?php

namespace Spray\Ouro\Transport\Message;

final class CreatePersistentSubscriptionCompleted
{
    /**
     * @var CreatePersistentSubscriptionResult
     */
    private $result;

    /**
     * @var string
     */
    private $reason;

    /**
     * @param CreatePersistentSubscriptionResult $result
     * @param string $reason
     */
    public function __construct(CreatePersistentSubscriptionResult $result, string $reason)
    {
        $this->result = $result;
        $this->reason = $reason;
    }

    /**
     * @return CreatePersistentSubscriptionResult
     */
    public function getResult(): CreatePersistentSubscriptionResult
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
