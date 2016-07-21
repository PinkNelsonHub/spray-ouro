<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Generator;
use GuzzleHttp\Psr7\Request;
use Icicle\Coroutine\Coroutine;
use Mhwk\Ouro\Transport\Message\ReadStreamEventsComplete;
use Mhwk\Ouro\Transport\Message\ReadStreamEventsForward;
use Mhwk\Ouro\Transport\Message\ReadStreamResult;

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
        $response = yield from $this->send(new Request(
            'GET',
            sprintf(
                '/streams/%s/%s/forward/%s?embed=body',
                $command->getStream(),
                $command->getStart(),
                $command->getCount()
            ),
            [
                'Accept' => 'application/vnd.eventstore.atom+json'
            ]
        ));

        $this->assertResponse($response);

        $data = json_decode($response->getBody()->getContents(), true);

        return new ReadStreamEventsComplete(
            $this->buildEvents($data['entries']),
            new ReadStreamResult(0),
            $data['headOfStream'],
            ''
        );
    }
}
