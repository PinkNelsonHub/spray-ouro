<?php

namespace Spray\Ouro\Client;

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
    public function subscribePersistent(
        string $subscriptionId,
        string $streamId,
        int $allowedInFlightMessages,
        callable $onEventAppeared): Generator;

    /**
     * @param string $groupName
     * @param string $streamId
     * @param PersistentSubscriptionSettings $settings
     *
     * @return Generator
     */
    public function createPersistentSubscription(
        string $groupName,
        string $streamId,
        PersistentSubscriptionSettings $settings = null): Generator;

    /**
     * @param string $groupName
     * @param string $streamId
     * @param PersistentSubscriptionSettings $settings
     *
     * @return Generator
     */
    public function updatePersistentSubscription(
        string $groupName,
        string $streamId,
        PersistentSubscriptionSettings $settings = null): Generator;

    /**
     * @param string $groupName
     * @param string $streamId
     *
     * @return Generator
     */
    public function deletePersistentSubscription(
        string $groupName,
        string $streamId): Generator;
}
