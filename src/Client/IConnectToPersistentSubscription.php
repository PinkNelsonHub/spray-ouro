<?php

namespace Mhwk\Ouro\Client;

use Icicle\Coroutine\Coroutine;

interface IConnectToPersistentSubscription
{
    /**
     * Connect to a persistent subscription.
     *
     * @param string $subscriptionId
     * @param string $streamId
     * @param int $allowedInFlightMessages
     * @param callable $onEventAppeared
     *
     * @return Coroutine
     */
    public function subscribePersistent(
        string $subscriptionId,
        string $streamId,
        int $allowedInFlightMessages,
        callable $onEventAppeared): Coroutine;
}
