<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Generator;
use Spray\Ouro\Transport\Http\HttpRequest;
use Spray\Ouro\Transport\Message\PersistentSubscriptionAckEvents;

final class PersistentSubscriptionAckEventsHandler extends HttpEntriesHandler
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
        Assertion::isInstanceOf($command, PersistentSubscriptionAckEvents::class);
    }

    /**
     * Handle the command.
     *
     * @param PersistentSubscriptionAckEvents $command
     *
     * @return Generator
     */
    function request($command)
    {
        foreach ($command->getProcessedEventIds() as $processedEventId) {
            $response = yield from $this->send(HttpRequest::post(
                sprintf(
                    '/subscriptions/%s/%s/ack/%s',
                    $command->getEventStreamId(),
                    $command->getSubscriptionId(),
                    $processedEventId
                )
            ));
        }
    }
}
