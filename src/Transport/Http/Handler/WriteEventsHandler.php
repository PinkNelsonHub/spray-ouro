<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use GuzzleHttp\Psr7\Request;
use Mhwk\Ouro\Transport\Message\NewEvent;
use Mhwk\Ouro\Transport\Message\OperationResult;
use Mhwk\Ouro\Transport\Message\WriteEvents;
use Mhwk\Ouro\Transport\Message\WriteEventsCompleted;

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
        $response = $this->send(new Request(
            'POST',
            '/streams/' . $command->getEventStreamId(),
            [
                'Content-Type' => 'application/vnd.eventstore.events+json'
            ],
            $this->buildBody($command->getNewEvents())
        ));

        $this->assertResponse($response);

        yield new WriteEventsCompleted(new OperationResult(OperationResult::SUCCESS), '');
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
