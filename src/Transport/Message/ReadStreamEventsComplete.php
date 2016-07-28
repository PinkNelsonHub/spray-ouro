<?php

namespace Spray\Ouro\Transport\Message;

final class ReadStreamEventsComplete
{
    /**
     * @var ResolvedIndexedEvent[]
     */
    private $events = [];

    /**
     * @var ReadStreamResult
     */
    private $result;

    /**
     * @var bool
     */
    private $isEndOfStream;

    /**
     * @var string
     */
    private $error;

    /**
     * @param array $events
     * @param ReadStreamResult $result
     * @param bool $isEndOfStream
     * @param string $error
     */
    public function __construct(array $events, ReadStreamResult $result, bool $isEndOfStream, string $error)
    {
        $this->setResolvedIndexEvents($events);
        $this->result = $result;
        $this->isEndOfStream = $isEndOfStream;
        $this->error = $error;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return ReadStreamResult
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return boolean
     */
    public function isEndOfStream()
    {
        return $this->isEndOfStream;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param ResolvedIndexedEvent[] $resolvedIndexes
     */
    private function setResolvedIndexEvents(array $resolvedIndexes)
    {
        foreach ($resolvedIndexes as $resolvedIndex) {
            $this->addResolvedIndexEvent($resolvedIndex);
        }
    }

    /**
     * @param ResolvedIndexedEvent $resolvedIndexEvent
     */
    private function addResolvedIndexEvent(ResolvedIndexedEvent $resolvedIndexEvent)
    {
        $this->events[] = $resolvedIndexEvent;
    }
}
