<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use GuzzleHttp\Psr7\Request;
use Mhwk\Ouro\Transport\Message\UpdatePersistentSubscription;
use Mhwk\Ouro\Transport\Message\UpdatePersistentSubscriptionCompleted;
use Mhwk\Ouro\Transport\Message\UpdatePersistentSubscriptionResult;

final class UpdatePersistentSubscriptionHandler extends HttpHandler
{

    /**
     * Assert that the command can be handled.
     *
     * @param object $command
     *
     * @return void
     */
    function assert($command)
    {
        Assertion::isInstanceOf($command, UpdatePersistentSubscription::class);
    }

    /**
     * Handle the command.
     *
     * @param object $command
     *
     * @return object
     */
    function request($command)
    {
        $response = yield $this->send(new Request(
            'POST',
            sprintf(
                '/subscriptions/%s/%s',
                $command->getEventStreamId(),
                $command->getSubscriptionGroupName()
            ),
            [
                'Content-Type' => 'application/json'
            ],
            json_encode([
                'resolveLinktos' => $command->isResolveLinkTos(),
                'startFrom' => $command->getStartFrom(),
                'extraStatistics' => $command->isRecordStatistics(),
                'checkPointAfterMilliseconds' => $command->getCheckPointAfterTime(),
                'liveBufferSize' => $command->getLiveBufferSize(),
                'readBatchSize' => $command->getReadBatchSize(),
                'bufferSize' => $command->getBufferSize(),
                'maxCheckPointCount' => $command->getCheckPointMaxCount(),
                'maxRetryCount' => $command->getMaxRetryCount(),
                'maxSubscriberCount' => $command->getSubscriberMaxCount(),
                'messageTimeoutMilliseconds' => $command->getMessageTimeoutMilliseconds(),
                'minCheckPointCount' => $command->getCheckPointMinCount(),
                'namedConsumerStrategy' => $command->getNamedConsumerStrategy()
            ])
        ));

        return new UpdatePersistentSubscriptionCompleted(UpdatePersistentSubscriptionResult::success(), '');
    }
}
