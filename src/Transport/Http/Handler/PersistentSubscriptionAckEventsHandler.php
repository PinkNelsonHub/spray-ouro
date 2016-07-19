<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Generator;
use GuzzleHttp\Psr7\Request;
use Icicle\Awaitable;
use Mhwk\Ouro\Message\PersistentSubscriptionAckEvents;

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
            $response = $this->send(new Request(
                'POST',
                sprintf(
                    '/subscriptions/%s/%s/ack/%s',
                    $command->getStreamId(),
                    $command->getSubscriptionId(),
                    $processedEventId
                )
            ));

            $this->assertResponse($response);

            yield $response;
        }
    }
}
