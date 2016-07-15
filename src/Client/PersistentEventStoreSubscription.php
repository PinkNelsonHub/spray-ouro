<?php

namespace Mhwk\Ouro\Client;

use Mhwk\Ouro\Message\NakAction;

final class PersistentEventStoreSubscription extends EventStoreSubscription
{
    /**
     * @var IConnectToPersistentSubscriptions
     */
    private $subscriptionOperation;

    /**
     * @param IConnectToPersistentSubscriptions $subscriptionOperation
     * @param string $streamId
     * @param int $lastCommitPosition
     * @param int $lastEventNumber
     */
    public function __construct(
        IConnectToPersistentSubscriptions $subscriptionOperation,
        string $streamId,
        int $lastCommitPosition,
        int $lastEventNumber)
    {
        parent::__construct($streamId, $lastCommitPosition, $lastEventNumber);
        $this->subscriptionOperation = $subscriptionOperation;
    }

    /**
     * @return void
     */
    function unsubscribe()
    {
        $this->subscriptionOperation->unsubscribe();
    }

    function notifyEventsProcessed(string ...$processedEvents)
    {
        $this->subscriptionOperation->notifyEventsProcessed(...$processedEvents);
    }

    function notifyEventsFailed(string $reason, NakAction $action, string ...$events)
    {
        $this->subscriptionOperation->notifyEventsFailed($reason, $action, ...$events);
    }
}
