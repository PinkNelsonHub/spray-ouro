<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use DateTimeImmutable;
use Mhwk\Ouro\Message\EventRecord;
use Mhwk\Ouro\Message\ResolvedIndexedEvent;

abstract class HttpEntriesHandler extends HttpHandler
{
    protected function buildEvents(array $entries)
    {
        $result = [];

        foreach (array_reverse($entries) as $entry) {
            $result[] = $this->buildEvent($entry);
        }

        return $result;
    }

    protected function buildEvent(array $entry)
    {
        return new ResolvedIndexedEvent(
            new EventRecord(
                $entry['streamId'],
                $entry['eventNumber'],
                $entry['eventId'],
                $entry['eventType'],
                json_decode($entry['data'], true) ?: [],
                json_decode($entry['metaData'], true) ?: [],
                DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.uP', $entry['updated'])
            )
        );
    }
}
