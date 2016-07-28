<?php

namespace Spray\Ouro\Client;

use Icicle\Coroutine\Coroutine;

interface IReadFromEventStoreAsync
{
    /**
     * @param string $stream
     * @param callable $onEventAppeared
     * @param int $start
     * @param int|null $limit
     *
     * @return Coroutine
     */
    function readStreamEventsForwardAsync(string $stream, callable $onEventAppeared, int $start = 0, int $limit = null): Coroutine;
}
