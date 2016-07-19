<?php

namespace Mhwk\Ouro\Client;

use Icicle\Coroutine\Coroutine;

interface IConfirmEvent
{
    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     *
     * @return Coroutine
     */
    public function acknowledge(string $subscriptionId, string $streamId, array $processedStreamIds): Coroutine;

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     * @param string $message
     * @param int $action
     *
     * @return Coroutine
     */
    public function fail(string $subscriptionId, string $streamId, array $processedStreamIds, string $message, int $action): Coroutine;
}
