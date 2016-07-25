<?php

namespace Mhwk\Ouro\Transport\Message;

final class DeletePersistentSubscription
{
    /**
     * @var string
     */
    private $subscriptionGroupName;

    /**
     * @var string
     */
    private $eventStreamId;

    /**
     * @param string $subscriptionGroupName
     * @param string $eventStreamId
     */
    public function __construct(string $subscriptionGroupName, string $eventStreamId)
    {
        $this->subscriptionGroupName = $subscriptionGroupName;
        $this->eventStreamId = $eventStreamId;
    }

    /**
     * @return string
     */
    public function getSubscriptionGroupName(): string
    {
        return $this->subscriptionGroupName;
    }

    /**
     * @return string
     */
    public function getEventStreamId(): string
    {
        return $this->eventStreamId;
    }
}
