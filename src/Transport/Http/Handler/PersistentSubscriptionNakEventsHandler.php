<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Generator;
use Spray\Ouro\Transport\Http\HttpRequest;
use Spray\Ouro\Transport\Message\PersistentSubscriptionNakEvents;

final class PersistentSubscriptionNakEventsHandler extends HttpEntriesHandler
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
        Assertion::isInstanceOf($command, PersistentSubscriptionNakEvents::class);
    }

    /**
     * Handle the command.
     *
     * @param PersistentSubscriptionNakEvents $command
     *
     * @return Generator
     */
    function request($command)
    {
        foreach ($command->getProcessedEventIds() as $processedEventId) {
            $response = yield from $this->send(HttpRequest::post(sprintf(
                    '/subscriptions/%s/%s/nack/%s',
                    $command->getEventStreamId(),
                    $command->getSubscriptionId(),
                    $processedEventId
                ))
                ->withQuery('action', $command->getAction())
            );
        }
    }
}
