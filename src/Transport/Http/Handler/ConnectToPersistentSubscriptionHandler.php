<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use GuzzleHttp\Psr7\Request;
use Mhwk\Ouro\Message\ConnectToPersistentSubscription;
use Mhwk\Ouro\Message\PersistentSubsciptionStreamEventAppeared;
use Mhwk\Ouro\Message\PersistentSubscriptionAckEvents;
use Mhwk\Ouro\Message\PersistentSubscriptionNakEvents;

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
        $response = $this->send(new Request(
            'GET',
            sprintf(
                '/subscriptions/%s/%s?embed=content&count=%s',
                $command->getEventStreamId(),
                $command->getSubscriptionId(),
                $command->getAllowedInFlightMessages()
            ),
            [
                'Accept' => 'application/vnd.eventstore.competingatom+json'
            ]
        ));

        $this->assertResponse($response);

        $data = json_decode($response->getBody()->getContents(), true);

        $running = true;

        while ($running) {
            $action = yield new PersistentSubsciptionStreamEventAppeared(
                $this->buildEvent($data['entries'][0])
            );

            if ($action instanceof PersistentSubscriptionAckEvents) {
                $this->ack($command->getEventStreamId(), $command->getSubscriptionId(), $action);
            } else if ($action instanceof PersistentSubscriptionNakEvents) {
                $this->nak($command->getEventStreamId(), $command->getSubscriptionId(), $action);
            }

            $running = false;
        }
    }

    private function ack($stream, $group, PersistentSubscriptionAckEvents $message)
    {
        $response = $this->send(new Request(
            'POST',
            sprintf(
                '/subscriptions/%s/%s/ack?ids=%s',
                $stream,
                $group,
                implode(',', $message->getProcessedEventIds()),
                count($message->getProcessedEventIds())
            )
        ));

        $this->assertResponse($response);
    }

    private function nak($stream, $group, PersistentSubscriptionNakEvents $message)
    {
        $response = $this->send(new Request(
            'POST',
            sprintf(
                '/subscriptions/%s/%s/nack?ids=%s&action=%s',
                $stream,
                $group,
                implode(',', $message->getProcessedEventIds()),
                $message->getAction(),
                count($message->getProcessedEventIds())
            )
        ));

        $this->assertResponse($response);
    }
}
