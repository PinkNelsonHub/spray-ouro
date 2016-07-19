<?php

namespace Mhwk\Ouro\Client;

use Icicle\Coroutine\Coroutine;

interface IReadFromEventStore
{
    /**
     * @param string $stream
     * @param callable $onEventAppeared
     * @param int $start
     * @param int|null $limit
     *
     * @return Coroutine
     */
    function readStreamEventsForward(string $stream, callable $onEventAppeared, int $start = 0, int $limit = null): Coroutine;
}
