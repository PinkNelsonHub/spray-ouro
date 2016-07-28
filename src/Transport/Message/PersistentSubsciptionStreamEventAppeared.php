<?php

namespace Spray\Ouro\Transport\Message;

final class PersistentSubsciptionStreamEventAppeared
{
    /**
     * @var ResolvedIndexedEvent
     */
    private $event;

    /**
     * @param ResolvedIndexedEvent $event
     */
    public function __construct(ResolvedIndexedEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @return ResolvedIndexedEvent
     */
    public function getEvent(): ResolvedIndexedEvent
    {
        return $this->event;
    }
}
