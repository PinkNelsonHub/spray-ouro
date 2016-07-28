<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Spray\Ouro\Transport\Message\CreatePersistentSubscription;
use Spray\Ouro\Transport\Message\CreatePersistentSubscriptionCompleted;
use Spray\Ouro\Transport\Message\CreatePersistentSubscriptionResult;

final class CreatePersistentSubscriptionHandler extends HttpHandler
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
        Assertion::isInstanceOf($command, CreatePersistentSubscription::class);
    }

    /**
     * Handle the command.
     *
     * @param CreatePersistentSubscription $command
     *
     * @return object
     */
    function request($command)
    {
        $response = yield $this->send(new Request(
            'PUT',
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

        return new CreatePersistentSubscriptionCompleted(CreatePersistentSubscriptionResult::success(), '');
    }

    /**
     * @param Response $response
     */
    protected function assertResponse(Response $response)
    {
        Assertion::inArray(
            $response->getStatusCode(),
            [200, 201, 202, 409],
            sprintf('Failed request [%s]: %s', $response->getStatusCode(), $response->getReasonPhrase())
        );
    }
}
