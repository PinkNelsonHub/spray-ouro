<?php

namespace Mhwk\Ouro\Transport\Http\Handler;

use Assert\Assertion;
use Assert\AssertionFailedException;
use DateTimeImmutable;
use Illuminate\Support\Facades\Log;
use Mhwk\Ouro\Transport\Message\EventRecord;
use Mhwk\Ouro\Transport\Message\ResolvedIndexedEvent;

abstract class HttpEntriesHandler extends HttpHandler
{
    protected function buildEvents(array $entries)
    {
        $result = [];

        foreach (array_reverse($entries) as $entry) {
            try {
                $this->assertEvent($entry);
                $result[] = $this->buildEvent($entry);
            } catch (AssertionFailedException $e) {
                Log::info($e->getMessage());
                continue;
            }
        }

        return $result;
    }

    protected function assertEvent(array $entry)
    {
        Assertion::keyExists($entry, 'data', 'Data missing for event');
        Assertion::keyExists($entry, 'metaData', 'Metadata missing for event');
    }

    protected function buildEvent(array $entry)
    {
        return new ResolvedIndexedEvent(
            new EventRecord(
                $entry['streamId'],
                $entry['eventNumber'],
                $this->determineEventId($entry),
                $entry['eventType'],
                json_decode($entry['data'], true) ?: [],
                json_decode($entry['metaData'], true) ?: [],
                DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.uP', $entry['updated'])
            )
        );
    }

    private function determineEventId($entry)
    {
        if (isset($entry['links'])) {
            foreach ($entry['links'] as $link) {
                if ('ack' === $link['relation']) {
                    $parts = explode('/', $link['uri']);
                    return array_pop($parts);
                }
            }
        }

        return $entry['eventId'];
    }
}
