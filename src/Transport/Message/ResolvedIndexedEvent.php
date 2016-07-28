<?php

namespace Spray\Ouro\Transport\Message;

final class ResolvedIndexedEvent
{
    /**
     * @var EventRecord
     */
    private $event;

    /**
     * @var EventRecord
     */
    private $link;

    /**
     * @param EventRecord $event
     * @param EventRecord $link
     */
    public function __construct(EventRecord $event, EventRecord $link = null)
    {
        $this->event = $event;
        $this->link = $link;
    }

    /**
     * @return EventRecord
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return EventRecord
     */
    public function getLink()
    {
        return $this->link;
    }
}
