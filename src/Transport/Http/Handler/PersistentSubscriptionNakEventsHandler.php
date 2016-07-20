<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Generator;
use GuzzleHttp\Psr7\Request;
use Mhwk\Ouro\Transport\Message\PersistentSubscriptionNakEvents;

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
            $response = $this->send(new Request(
                'POST',
                sprintf(
                    '/subscriptions/%s/%s/nack/%s?action=%s',
                    $command->getEventStreamId(),
                    $command->getSubscriptionId(),
                    $processedEventId,
                    $command->getAction()
                )
            ));

            $this->assertResponse($response);

            yield $response;
        }
    }
}
