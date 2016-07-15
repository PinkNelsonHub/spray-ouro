<?php

namespace Mhwk\Ouro\Message;

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
     * @var int
     */
    private $created;

    /**
     * @var int
     */
    private $createdEpoch;

    /**
     * @param string $eventStreamId
     * @param int $eventNumber
     * @param string $eventId
     * @param string $eventType
     * @param array $data
     * @param array $metadata
     * @param int $created
     * @param int $createdEpoch
     */
    public function __construct(string $eventStreamId, int $eventNumber, string $eventId, string $eventType, array $data, array $metadata, int $created, int $createdEpoch)
    {
        $this->eventStreamId = $eventStreamId;
        $this->eventNumber = $eventNumber;
        $this->eventId = $eventId;
        $this->eventType = $eventType;
        $this->data = $data;
        $this->metadata = $metadata;
        $this->created = $created;
        $this->createdEpoch = $createdEpoch;
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
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return int
     */
    public function getCreatedEpoch()
    {
        return $this->createdEpoch;
    }
}
