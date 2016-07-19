<?php

namespace Mhwk\Ouro\Message;

final class PersistentSubscriptionAckEvents
{
    /**
     * @var string
     */
    private $subscriptionId;

    /**
     * @var string
     */
    private $streamId;

    /**
     * @var array
     */
    private $processedEventIds;

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param string[] $processedEventIds
     */
    public function __construct(string $subscriptionId, string $streamId, array $processedEventIds)
    {
        $this->subscriptionId = $subscriptionId;
        $this->streamId = $streamId;
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
    public function getStreamId(): string
    {
        return $this->streamId;
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
