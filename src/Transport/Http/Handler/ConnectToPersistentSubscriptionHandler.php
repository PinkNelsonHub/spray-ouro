<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Assert\AssertionFailedException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Icicle\Awaitable;
use Icicle\Observable\Emitter;
use Spray\Ouro\Exception\EventStoreUnreachableException;
use Spray\Ouro\Exception\NonExistentStreamException;
use Spray\Ouro\Exception\NonExistentSubscriptionException;
use Spray\Ouro\Transport\Message\ConnectToPersistentSubscription;
use Spray\Ouro\Transport\Message\PersistentSubsciptionStreamEventAppeared;

final class ConnectToPersistentSubscriptionHandler extends HttpEntriesHandler
{
    private $running = true;

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
            while ($this->running) {
                try {
                    $response = yield from $this->send(new Request(
                        'GET',
                        sprintf(
                            '/subscriptions/%s/%s/%s?embed=body',
                            $command->getEventStreamId(),
                            $command->getSubscriptionId(),
                            $command->getAllowedInFlightMessages()
                        ),
                        [
                            'Accept' => 'application/vnd.eventstore.competingatom+json'
                        ]
                    ));

                    $data = json_decode($response->getBody()->getContents(), true);

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
                        yield Awaitable\resolve()->delay(.5);
                    }
                } catch (RequestException $error) {
                    if (404 === $error->getResponse()->getStatusCode()) {
                        throw new NonExistentSubscriptionException(
                            sprintf(
                                'Subscription %s-%s does not exist: %s',
                                $command->getEventStreamId(),
                                $command->getSubscriptionId(),
                                $error->getMessage()
                            ),
                            $error->getCode(),
                            $error
                        );
                    }
                    throw $error;
                }
            }
        });
    }
}
