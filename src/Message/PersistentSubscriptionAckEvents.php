<?php

namespace Mhwk\Ouro\Message;

final class PersistentSubscriptionAckEvents
{
    /**
     * @var string
     */
    private $subscriptionId;

    /**
     * @var array
     */
    private $processedEventIds;

    /**
     * @param string $subscriptionId
     * @param string[] $processedEventIds
     */
    public function __construct(string $subscriptionId, array $processedEventIds)
    {
        $this->subscriptionId = $subscriptionId;
        $this->setProcessedEventIds($processedEventIds);
    }

    /**
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @return array
     */
    public function getProcessedEventIds()
    {
        return $this->processedEventIds;
    }

    /**
     * @param array $processedEventIds
     */
    private function setProcessedEventIds(array $processedEventIds)
    {
        foreach ($processedEventIds as $processedEventId) {
            $this->addProcessedEventId($processedEventId);
        }
    }

    /**
     * @param string $processedEventId
     */
    private function addProcessedEventId(string $processedEventId)
    {
        $this->processedEventIds[] = $processedEventId;
    }
}
