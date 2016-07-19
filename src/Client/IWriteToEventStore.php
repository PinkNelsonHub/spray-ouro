<?php

namespace Mhwk\Ouro\Client;

use Icicle\Coroutine\Coroutine;

interface IWriteToEventStore
{
    /**
     * @param string $stream
     * @param int $expectedEventNumber
     * @param array $events
     *
     * @return Coroutine
     */
    function writeEvents(string $stream, int $expectedEventNumber, array $events): Coroutine;
}
