<?php

namespace Mhwk\Ouro\Client;

interface IConnectToPersistentSubscriptions
{
    function unsubscribe();

    function notifyEventsProcessed(string ...$processedEvents);

    function notifyEventsFailed(string $reason, NakAction $action, string ...$events);
}
