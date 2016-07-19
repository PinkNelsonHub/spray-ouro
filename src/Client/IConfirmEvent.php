<?php

namespace Mhwk\Ouro\Client;

interface IConfirmEvent
{
    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     */
    public function acknowledge(string $subscriptionId, string $streamId, array $processedStreamIds);

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     * @param string $message
     * @param int $action
     */
    public function fail(string $subscriptionId, string $streamId, array $processedStreamIds, string $message, int $action);
}
