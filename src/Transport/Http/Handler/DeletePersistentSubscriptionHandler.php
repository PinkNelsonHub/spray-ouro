<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use GuzzleHttp\Psr7\Request;
use Mhwk\Ouro\Transport\Message\DeletePersistentSubscription;
use Mhwk\Ouro\Transport\Message\DeletePersistentSubscriptionCompleted;
use Mhwk\Ouro\Transport\Message\DeletePersistentSubscriptionResult;

final class DeletePersistentSubscriptionHandler extends HttpHandler
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
        Assertion::isInstanceOf($command, DeletePersistentSubscription::class);
    }

    /**
     * Handle the command.
     *
     * @param object $command
     *
     * @return object
     */
    function request($command)
    {
        $response = yield $this->send(new Request(
            'DELETE',
            sprintf(
                '/subscriptions/%s/%s',
                $command->getEventStreamId(),
                $command->getSubscriptionGroupName()
            ),
            [
                'Content-Type' => 'application/json'
            ]
        ));

        $this->assertResponse($response);

        yield new DeletePersistentSubscriptionCompleted(DeletePersistentSubscriptionResult::success(), '');
    }
}
