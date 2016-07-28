<?php

namespace Spray\Ouro\Client;

use Generator;

interface IWriteToEventStore
{
    /**
     * @param string $stream
     * @param int $expectedEventNumber
     * @param array $events
     *
     * @return Generator
     */
    function writeEvents(string $stream, int $expectedEventNumber, array $events): Generator;
}
