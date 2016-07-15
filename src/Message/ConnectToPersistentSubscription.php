<?php

namespace Mhwk\Ouro\Message;

final class ConnectToPersistentSubscription
{
    /**
     * @var string
     */
    private $subscriptionId;

    /**
     * @var string
     */
    private $eventStreamId;

    /**
     * @var int
     */
    private $allowedInFlightMessages;

    /**
     * @param string $subscriptionId
     * @param string $eventStreamId
     * @param int $allowedInFlightMessages
     */
    public function __construct(string $subscriptionId, string $eventStreamId, int $allowedInFlightMessages)
    {
        $this->subscriptionId = $subscriptionId;
        $this->eventStreamId = $eventStreamId;
        $this->allowedInFlightMessages = $allowedInFlightMessages;
    }

    /**
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @return string
     */
    public function getEventStreamId()
    {
        return $this->eventStreamId;
    }

    /**
     * @return int
     */
    public function getAllowedInFlightMessages()
    {
        return $this->allowedInFlightMessages;
    }
}
