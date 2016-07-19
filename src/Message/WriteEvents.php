<?php

namespace Mhwk\Ouro\Message;

final class WriteEvents
{
    /**
     * @var string
     */
    private $eventStreamId;

    /**
     * @var int
     */
    private $expectedVersion;

    /**
     * @var NewEvent[]
     */
    private $newEvents = [];

    /**
     * @var bool
     */
    private $requireMaster;

    /**
     * @param string $eventStreamId
     * @param int $expectedVersion
     * @param NewEvent[] $newEvents
     * @param bool $requireMaster
     */
    public function __construct(string $eventStreamId, int $expectedVersion, array $newEvents, bool $requireMaster)
    {
        $this->eventStreamId = $eventStreamId;
        $this->expectedVersion = $expectedVersion;
        $this->requireMaster = $requireMaster;
        $this->setNewEvents($newEvents);
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
    public function getExpectedVersion()
    {
        return $this->expectedVersion;
    }

    /**
     * @return NewEvent[]
     */
    public function getNewEvents()
    {
        return $this->newEvents;
    }

    /**
     * @return boolean
     */
    public function isRequireMaster()
    {
        return $this->requireMaster;
    }

    /**
     * @param array $newEvents
     */
    private function setNewEvents(array $newEvents)
    {
        foreach ($newEvents as $newEvent) {
            $this->addNewEvent($newEvent);
        }
    }

    /**
     * @param NewEvent $newEvent
     */
    private function addNewEvent(NewEvent $newEvent)
    {
        $this->newEvents[] = $newEvent;
    }
}
