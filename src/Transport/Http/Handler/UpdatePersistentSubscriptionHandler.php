<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Spray\Ouro\Transport\Http\HttpRequest;
use Spray\Ouro\Transport\Message\UpdatePersistentSubscription;
use Spray\Ouro\Transport\Message\UpdatePersistentSubscriptionCompleted;
use Spray\Ouro\Transport\Message\UpdatePersistentSubscriptionResult;

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
        $response = yield $this->send(HttpRequest::post(sprintf(
                '/subscriptions/%s/%s',
                $command->getEventStreamId(),
                $command->getSubscriptionGroupName()
            ))
            ->withContentType('application/json')
            ->withJsonBody([
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
            ]));

        return new UpdatePersistentSubscriptionCompleted(UpdatePersistentSubscriptionResult::success(), '');
    }
}
