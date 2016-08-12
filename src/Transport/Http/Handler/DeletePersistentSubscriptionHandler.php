<?php

namespace Spray\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Spray\Ouro\Transport\Http\HttpRequest;
use Spray\Ouro\Transport\Message\DeletePersistentSubscription;
use Spray\Ouro\Transport\Message\DeletePersistentSubscriptionCompleted;
use Spray\Ouro\Transport\Message\DeletePersistentSubscriptionResult;

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
        $response = yield from $this->send(HttpRequest::delete(sprintf(
                '/subscriptions/%s/%s',
                $command->getEventStreamId(),
                $command->getSubscriptionGroupName()
            ))
            ->withContentType('application/json'));

        yield new DeletePersistentSubscriptionCompleted(DeletePersistentSubscriptionResult::success(), '');
    }
}
