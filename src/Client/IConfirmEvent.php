<?php

namespace Spray\Ouro\Client;

use Generator;

interface IConfirmEvent
{
    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     *
     * @return Generator
     */
    public function acknowledge(
        string $subscriptionId,
        string $streamId,
        array $processedStreamIds): Generator;

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     * @param string $message
     * @param int $action
     *
     * @return Generator
     */
    public function fail(
        string $subscriptionId,
        string $streamId,
        array $processedStreamIds,
        string $message,
        int $action): Generator;
}
