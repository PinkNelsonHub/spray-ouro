<?php

namespace Spray\Ouro\Client;

use Icicle\Coroutine\Coroutine;

interface IConnectToPersistentSubscriptionAsync
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
    public function subscribePersistentAsync(
        string $subscriptionId,
        string $streamId,
        int $allowedInFlightMessages,
        callable $onEventAppeared,
        callable $onSubscriptionDropped,
        bool $autoAck = true): Coroutine;

    /**
     * @param string $groupName
     * @param string $streamId
     * @param PersistentSubscriptionSettings $settings
     *
     * @return Coroutine
     */
    public function createPersistentSubscriptionAsync(
        string $groupName,
        string $streamId,
        PersistentSubscriptionSettings $settings = null): Coroutine;

    /**
     * @param string $groupName
     * @param string $streamId
     * @param PersistentSubscriptionSettings $settings
     *
     * @return Coroutine
     */
    public function updatePersistentSubscriptionAsync(
        string $groupName,
        string $streamId,
        PersistentSubscriptionSettings $settings = null): Coroutine;

    /**
     * @param string $groupName
     * @param string $streamId
     *
     * @return Coroutine
     */
    public function deletePersistentSubscriptionAsync(
        string $groupName,
        string $streamId): Coroutine;
}
