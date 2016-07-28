<?php

namespace Spray\Ouro\Transport\Message;

final class NewEvent
{
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
     * @param string $eventId
     * @param string $eventType
     * @param array $data
     * @param array $metadata
     */
    public function __construct(string $eventId, string $eventType, array $data, array $metadata)
    {
        $this->eventId = $eventId;
        $this->eventType = $eventType;
        $this->data = $data;
        $this->metadata = $metadata;
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
}
