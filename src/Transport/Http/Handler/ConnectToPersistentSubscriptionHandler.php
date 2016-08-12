<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Icicle\Awaitable;
use Icicle\Observable\Emitter;
use Spray\Ouro\Transport\Http\HttpRequest;
use Spray\Ouro\Transport\Http\HttpResponse;
use Spray\Ouro\Transport\Message\ConnectToPersistentSubscription;
use Spray\Ouro\Transport\Message\PersistentSubsciptionStreamEventAppeared;

final class ConnectToPersistentSubscriptionHandler extends HttpEntriesHandler
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
        Assertion::isInstanceOf($command, ConnectToPersistentSubscription::class);
    }

    /**
     * Handle the command.
     *
     * @param ConnectToPersistentSubscription $command
     *
     * @return object
     */
    function request($command)
    {
        return new Emitter(function(callable $emit) use ($command) {
            while (true) {
                /** @var HttpResponse $response */
                $response = yield from $this->send(HttpRequest::get(sprintf(
                        '/subscriptions/%s/%s/%s',
                        $command->getEventStreamId(),
                        $command->getSubscriptionId(),
                        $command->getAllowedInFlightMessages()
                    ))
                    ->withQuery('embed', 'body')
                    ->withAccept('application/vnd.eventstore.competingatom+json')
                    ->withLongPoll(30));

                $data = json_decode($response->getBody(), true);

                if (count($data['entries'])) {
                    foreach (array_reverse($data['entries']) as $entry) {
                        try {
                            $this->assertEvent($entry);
                            yield $emit(new PersistentSubsciptionStreamEventAppeared(
                                $this->buildEvent($entry)
                            ));
                        } catch (AssertionFailedException $e) {
                            continue;
                        }
                    }
                } else {
                    yield Awaitable\resolve();
                }
            }
        });
    }
}
