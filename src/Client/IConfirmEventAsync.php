<?php

namespace Mhwk\Ouro\Client;

use Icicle\Coroutine\Coroutine;

interface IConfirmEventAsync
{
    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     *
     * @return Coroutine
     */
    public function acknowledgeAsync(
        string $subscriptionId,
        string $streamId,
        array $processedStreamIds): Coroutine;

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     * @param string $message
     * @param int $action
     *
     * @return Coroutine
     */
    public function failAsync(
        string $subscriptionId,
        string $streamId,
        array $processedStreamIds,
        string $message,
        int $action): Coroutine;
}
