<?php

namespace Mhwk\Ouro\Client;

use Closure;
use Exception;
use Mhwk\Ouro\Async\Async;
use Mhwk\Ouro\Async\ITask;
use Mhwk\Ouro\Message\NakAction;
use Mhwk\Ouro\Message\ResolvedIndexedEvent;
use Mhwk\Ouro\Message\SubscriptionDropReason;
use SplQueue;

abstract class EventStorePersistentSubscriptionBase
{
    /**
     * @var string
     */
    private $subscriptionId;

    /**
     * @var string
     */
    private $streamId;

    /**
     * @var Closure
     */
    private $eventAppeared;

    /**
     * @var Closure
     */
    private $subscriptionDropped;

    /**
     * @var int
     */
    private $bufferSize;

    /**
     * @var bool
     */
    private $autoAck;

    /**
     * @var PersistentEventStoreSubscription
     */
    private $subscription;

    /**
     * @var SplQueue
     */
    private $queue;

    /**
     * @var bool
     */
    private $processing = false;

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param string $groupName
     * @param Closure $eventAppeared
     * @param Closure $subscriptionDropped
     * @param int $bufferSize
     * @param bool $autoAck
     */
    protected function __construct(
        string $subscriptionId,
        string $streamId,
        Closure $eventAppeared,
        Closure $subscriptionDropped,
        int $bufferSize,
        bool $autoAck = true)
    {
        $this->subscriptionId = $subscriptionId;
        $this->streamId = $streamId;
        $this->eventAppeared = $eventAppeared;
        $this->subscriptionDropped = $subscriptionDropped;
        $this->bufferSize = $bufferSize;
        $this->autoAck = $autoAck;
        $this->queue = new SplQueue();
    }

    function start(): ITask
    {
        $task = $this->startSubscription(
            $this->subscriptionId,
            $this->streamId,
            $this->bufferSize,
            function(EventStoreSubscription $subscription, ResolvedIndexedEvent $resolvedEvent) {
                $this->onEventAppeared($subscription, $resolvedEvent);
            },
            function(EventStoreSubscription $subscription, SubscriptionDropReason $reason, Exception $exception) {
                $this->onSubscriptionDropped($subscription, $reason, $exception);
            }
        );
        $task->continueWith(function(ITask $task) {
            $this->subscription = $task->result();
        });
        return $task;
    }

    function stop()
    {

    }

    protected abstract function startSubscription(
        string $subscriptionId,
        string $streamId,
        int $bufferSize,
        Closure $onEventAppeared,
        Closure $onSubscriptionDropped
    ): ITask;

    private function dropSubscription(SubscriptionDropReason $reason, Exception $error)
    {
        if (null !== $this->subscription) {
            $this->subscription->unsubscribe();
        }
        if (null !== $this->subscriptionDropped) {
            $action = $this->subscriptionDropped;
            $action($this, $reason, $error);
        }
    }

    function acknowledge(ResolvedIndexedEvent ...$events)
    {
        $this->subscription->notifyEventsProcessed($events);
    }

    function fail(NakAction $action, NakAction $reason, ResolvedIndexedEvent ...$events)
    {
        $this->subscription->notifyEventsFailed($action, $reason, $events);
    }

    private function enqueue(ResolvedIndexedEvent $resolvedEvent)
    {
        $this->queue->enqueue($resolvedEvent);
        if ( ! $this->processing) {
            Async::threadPool()->queueUserWorkItem(function () {
                $this->processQueue();
            });
        }
    }

    private function onEventAppeared(EventStoreSubscription $subscription, ResolvedIndexedEvent $resolvedEvent)
    {
        $this->enqueue($resolvedEvent);
    }

    private function onSubscriptionDropped(EventStoreSubscription $subscription, SubscriptionDropReason $reason, Exception $exception)
    {

    }

    /**
     *
     */
    private function processQueue()
    {
        do {
            $this->processing = true;
            if (null === $this->subscription) {
                usleep(1);
            } else {
                try {

                    $event = $this->queue->dequeue();
                    $action = $this->eventAppeared;
                    $action($this, $event);

                    if ($this->autoAck) {
                        $this->acknowledge($event);
                    }
                } catch (Exception $error) {
                    $this->dropSubscription(
                        new SubscriptionDropReason(SubscriptionDropReason::EVENT_HANDLER_EXCEPTION),
                        $error
                    );
                    return;
                }
            }
        } while ($this->queue->count() && $this->processing);
    }
}
