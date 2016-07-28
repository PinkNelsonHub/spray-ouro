<?php

namespace Spray\Ouro\Transport\Message;

use DateTimeImmutable;

final class EventRecord
{
    /**
     * @var string
     */
    private $eventStreamId;

    /**
     * @var int
     */
    private $eventNumber;

    /**
     * @var string
     */
    private $eventId;

    /**
     * @var string
     */
    private $eventType;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @var DateTimeImmutable
     */
    private $created;

    /**
     * @param string $eventStreamId
     * @param int $eventNumber
     * @param string $eventId
     * @param string $eventType
     * @param array $data
     * @param array $metadata
     * @param DateTimeImmutable $created
     */
    public function __construct(string $eventStreamId, int $eventNumber, string $eventId, string $eventType, array $data, array $metadata, DateTimeImmutable $created)
    {
        $this->eventStreamId = $eventStreamId;
        $this->eventNumber = $eventNumber;
        $this->eventId = $eventId;
        $this->eventType = $eventType;
        $this->data = $data;
        $this->metadata = $metadata;
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getEventStreamId()
    {
        return $this->eventStreamId;
    }

    /**
     * @return int
     */
    public function getEventNumber()
    {
        return $this->eventNumber;
    }

    /**
     * @return string
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreated()
    {
        return $this->created;
    }
}
