<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Mhwk\Ouro\Message\EventRecord;
use Mhwk\Ouro\Message\ResolvedIndexedEvent;

abstract class HttpEntriesHandler extends HttpHandler
{
    protected function buildEvents(array $entries)
    {
        $result = [];

        foreach ($entries as $entry) {
            $result[] = $this->buildEvent($entry);
        }

        return $result;
    }

    protected function buildEvent(array $entry)
    {
        return new ResolvedIndexedEvent(
            new EventRecord(
                $entry['content']['eventStreamId'],
                $entry['content']['eventNumber'],
                $entry['content']['eventId'],
                $entry['content']['eventType'],
                $entry['content']['data'] ?: [],
                $entry['content']['metadata'] ?: [],
                0,
                0
            )
        );
    }
}
