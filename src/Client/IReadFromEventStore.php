<?php

namespace Mhwk\Ouro\Client;

use Generator;

interface IReadFromEventStore
{
    /**
     * @param string $stream
     * @param callable $onEventAppeared
     * @param int $start
     * @param int|null $limit
     *
     * @return Generator
     */
    function readStreamEventsForward(
        string $stream,
        callable $onEventAppeared,
        int $start = 0,
        int $limit = null): Generator;
}
