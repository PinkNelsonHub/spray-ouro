<?php

namespace Mhwk\Ouro\Transport\Message;

final class PersistentSubscriptionAckEvents
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
     * @var array
     */
    private $processedEventIds;

    /**
     * @param string $subscriptionId
     * @param string $eventStreamId
     * @param string[] $processedEventIds
     */
    public function __construct(string $subscriptionId, string $eventStreamId, array $processedEventIds)
    {
        $this->subscriptionId = $subscriptionId;
        $this->eventStreamId = $eventStreamId;
        $this->setProcessedEventIds($processedEventIds);
    }

    /**
     * @return string
     */
    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    /**
     * @return string
     */
    public function getEventStreamId(): string
    {
        return $this->eventStreamId;
    }

    /**
     * @return array
     */
    public function getProcessedEventIds(): array
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
