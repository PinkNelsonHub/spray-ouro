<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use GuzzleHttp\Psr7\Request;
use Spray\Ouro\Transport\Message\NewEvent;
use Spray\Ouro\Transport\Message\OperationResult;
use Spray\Ouro\Transport\Message\WriteEvents;
use Spray\Ouro\Transport\Message\WriteEventsCompleted;

final class WriteEventsHandler extends HttpHandler
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
        Assertion::isInstanceOf($command, WriteEvents::class);
    }

    /**
     * Handle the command.
     *
     * @param WriteEvents $command
     *
     * @return object
     */
    function request($command)
    {
        $response = yield from $this->send(new Request(
            'POST',
            '/streams/' . $command->getEventStreamId(),
            [
                'Content-Type' => 'application/vnd.eventstore.events+json'
            ],
            $this->buildBody($command->getNewEvents())
        ));

        $this->assertResponse($response);

        return new WriteEventsCompleted(new OperationResult(OperationResult::SUCCESS), '');
    }

    /**
     * @param array $newEvents
     *
     * @return string
     */
    private function buildBody(array $newEvents)
    {
        /** @var NewEvent $newEvent */
        $result = [];

        foreach ($newEvents as $newEvent) {
            $result[] = [
                'eventId' => $newEvent->getEventId(),
                'eventType' => $newEvent->getEventType(),
                'data' => $newEvent->getData(),
                'metadata' => $newEvent->getMetadata()
            ];
        }

        return json_encode($result);
    }
}
