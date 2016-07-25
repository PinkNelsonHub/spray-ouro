<?php

namespace Mhwk\Ouro\Client;

use Icicle\Coroutine\Coroutine;

interface IWriteToEventStoreAsync
{
    /**
     * @param string $stream
     * @param int $expectedEventNumber
     * @param array $events
     *
     * @return Coroutine
     */
    function writeEventsAsync(string $stream, int $expectedEventNumber, array $events): Coroutine;
}
