<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Generator;
use Spray\Ouro\Transport\Http\HttpRequest;
use Spray\Ouro\Transport\Http\HttpResponse;
use Spray\Ouro\Transport\Message\ReadStreamEventsComplete;
use Spray\Ouro\Transport\Message\ReadStreamEventsForward;
use Spray\Ouro\Transport\Message\ReadStreamResult;

final class ReadStreamEventsForwardHandler extends HttpEntriesHandler
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
        Assertion::isInstanceOf($command, ReadStreamEventsForward::class);
    }

    /**
     * Handle the command.
     *
     * @param ReadStreamEventsForward $command
     *
     * @return Generator
     */
    function request($command)
    {
        /** @var HttpResponse $response */
        $response = yield from $this->send(HttpRequest::get(sprintf(
                '/streams/%s/%s/forward/%s',
                $command->getEventStreamId(),
                $command->getStart(),
                $command->getCount()
            ))
            ->withQuery('embed', 'body')
            ->withAccept('application/vnd.eventstore.atom+json'));

        $data = json_decode($response->getBody(), true);

        return new ReadStreamEventsComplete(
            $this->buildEvents($data['entries']),
            new ReadStreamResult(0),
            $data['headOfStream'],
            ''
        );
    }
}
