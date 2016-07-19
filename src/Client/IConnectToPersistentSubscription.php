<?php

namespace Mhwk\Ouro\Client;

use Generator;

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
     * @return Generator
     */
    public function connect(
        string $subscriptionId,
        string $streamId,
        int $allowedInFlightMessages,
        callable $onEventAppeared): Generator;
}
