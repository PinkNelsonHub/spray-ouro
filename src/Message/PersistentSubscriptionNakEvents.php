<?php

namespace Mhwk\Ouro\Message;

final class PersistentSubscriptionNakEvents
{
    /**
     * @var string
     */
    private $subscriptionId;

    /**
     * @var string[]
     */
    private $processedEventIds;

    /**
     * @var string
     */
    private $message;
    
    /**
     * @var NakAction
     */
    private $action;

    /**
     * @param string $subscriptionId
     * @param array $processedEventIds
     * @param string $message
     * @param NakAction $action
     */
    public function __construct(string $subscriptionId, array $processedEventIds, string $message, NakAction $action)
    {
        $this->subscriptionId = $subscriptionId;
        $this->setProcessedEventIds($processedEventIds);
        $this->message = $message;
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    /**
     * @return \string[]
     */
    public function getProcessedEventIds(): array
    {
        return $this->processedEventIds;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return NakAction
     */
    public function getAction(): NakAction
    {
        return $this->action;
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
