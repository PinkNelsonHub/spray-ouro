<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use GuzzleHttp\Psr7\Request;
use Icicle\Observable\Emitter;
use Mhwk\Ouro\Message\ReadStreamEventsComplete;
use Mhwk\Ouro\Message\ReadStreamEventsForward;
use Mhwk\Ouro\Message\ReadStreamResult;

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
     * @return object
     */
    function request($command)
    {
        return new Emitter(function(callable $emit) use ($command) {
            $head = false;
            $start = $command->getStart();
            $count = $command->getCount();

            while ( ! $head) {
                $response = $this->send(new Request(
                    'GET',
                    sprintf(
                        '/streams/%s/%s/forward/%s?embed=content',
                        $command->getStream(),
                        $start,
                        $count
                    ),
                    [
                        'Accept' => 'application/vnd.eventstore.atom+json'
                    ]
                ));

                $this->assertResponse($response);

                $data = json_decode($response->getBody()->getContents(), true);

                yield $emit(new ReadStreamEventsComplete(
                    $this->buildEvents($data['entries']),
                    new ReadStreamResult(0),
                    $data['headOfStream'],
                    ''
                ));

                $head = $data['headOfStream'];
                $start += $count;
            }
        });
    }
}
