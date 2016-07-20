<?php

namespace Mhwk\Ouro\Client;

use Generator;
use Icicle\Observable\Observable;
use Illuminate\Support\Facades\Log;
use Mhwk\Ouro\Exception\EventNotExecutedException;
use Mhwk\Ouro\Exception\EventNotSupportedException;
use Mhwk\Ouro\Transport\Message\ConnectToPersistentSubscription;
use Mhwk\Ouro\Transport\Message\NakAction;
use Mhwk\Ouro\Transport\Message\NewEvent;
use Mhwk\Ouro\Transport\Message\PersistentSubscriptionAckEvents;
use Mhwk\Ouro\Transport\Message\PersistentSubscriptionNakEvents;
use Mhwk\Ouro\Transport\Message\ReadStreamEventsComplete;
use Mhwk\Ouro\Transport\Message\ReadStreamEventsForward;
use Mhwk\Ouro\Transport\Message\ResolvedIndexedEvent;
use Mhwk\Ouro\Transport\Message\WriteEvents;
use Mhwk\Ouro\Transport\Http\HttpTransport;
use Mhwk\Ouro\Transport\IHandleMessage;
use Throwable;
use Icicle\Coroutine;

final class Connection
    implements IConnectToEventStore,
               IConnectedToEventStore
{
    /**
     * @var IHandleMessage
     */
    private $transport;

    /**
     * @param IHandleMessage $transport
     */
    private function __construct(
        IHandleMessage $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     *
     * @return IConnectedToEventStore
     */
    static function connect(string $host, string $username, string $password): IConnectedToEventStore
    {
        return new Connection(HttpTransport::factory($host, $username, $password));
    }

    /**
     * @param string $stream
     * @param int $expectedEventNumber
     * @param NewEvent[] $events
     *
     * @return Coroutine\Coroutine
     */
    function writeEvents(string $stream, int $expectedEventNumber, array $events): Coroutine\Coroutine
    {
        return Coroutine\create(function() use ($stream, $expectedEventNumber, $events) {
            yield $this->transport->handle(new WriteEvents($stream, $expectedEventNumber, $events, false));
        });
    }

    /**
     * @param string $stream
     * @param callable $onEventAppeared
     *
     * @return Coroutine\Coroutine
     */
    function readStreamEventsForward(string $stream, callable $onEventAppeared, int $start = 0, int $limit = null): Coroutine\Coroutine
    {
        return Coroutine\create(function() use ($stream, $onEventAppeared, $start, $limit) {
            $head = false;
            $count = 50;

            while ( ! $head) {
                /** @var ReadStreamEventsComplete $readStreamEventsComplete */
                /** @var ResolvedIndexedEvent $resolvedIndexEvent */
                if (null !== $limit && $start + $count >= $limit) {
                    $count = $limit - $start;
                }

                $readStreamEventsComplete = yield $this->transport->handle(new ReadStreamEventsForward(
                    $stream,
                    $start,
                    $count,
                    false
                ));

                foreach ($readStreamEventsComplete->getEvents() as $resolvedIndexEvent) {
                    $result = $onEventAppeared($resolvedIndexEvent->getEvent());
                    if ($result instanceof Generator) {
                        yield from $result;
                    } else {
                        yield;
                    }
                }

                $head = $readStreamEventsComplete->isEndOfStream();
                $start += $count;

                if (null !== $limit && $start + $count >= $limit) {
                    break;
                }
            }
        });
    }

    /**
     * Connect to a persistent subscription.
     *
     * @param string $subscriptionId
     * @param string $streamId
     * @param int $allowedInFlightMessages
     * @param callable $onEventAppeared
     *
     * @return Coroutine\Coroutine
     */
    function subscribePersistent(
        string $subscriptionId,
        string $streamId,
        int $allowedInFlightMessages,
        callable $onEventAppeared): Coroutine\Coroutine
    {
        return Coroutine\create(function() use ($subscriptionId, $streamId, $allowedInFlightMessages, $onEventAppeared) {
            /** @var Observable $observable */
            $observable = $this->transport->handle(new ConnectToPersistentSubscription($subscriptionId, $streamId, $allowedInFlightMessages));
            $iterator = $observable->getIterator();

            while (yield $iterator->isValid()) {
                try {
                    yield $onEventAppeared($iterator->getCurrent()->getEvent()->getEvent());
                    yield $this->acknowledge(
                        $subscriptionId,
                        $streamId,
                        [$iterator->getCurrent()->getEvent()->getEvent()->getEventId()]
                    );
                } catch (EventNotSupportedException $error) {
                    yield $this->fail(
                        $subscriptionId,
                        $streamId,
                        [$iterator->getCurrent()->getEvent()->getEvent()->getEventId()],
                        $error->getMessage(),
                        NakAction::SKIP
                    );
                } catch (EventNotExecutedException $error) {
                    yield $this->fail(
                        $subscriptionId,
                        $streamId,
                        [$iterator->getCurrent()->getEvent()->getEvent()->getEventId()],
                        $error->getMessage(),
                        NakAction::RETRY
                    );
                } catch (Throwable $error) {
                    yield $this->fail(
                        $subscriptionId,
                        $streamId,
                        [$iterator->getCurrent()->getEvent()->getEvent()->getEventId()],
                        $error->getMessage(),
                        NakAction::PARK
                    );
                    throw $error;
                }
            }
        });
    }

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     *
     * @return Coroutine\Coroutine
     */
    function acknowledge(string $subscriptionId, string $streamId, array $processedStreamIds): Coroutine\Coroutine
    {
        return Coroutine\create(function() use ($subscriptionId, $streamId, $processedStreamIds) {
            yield from $this->transport->handle(new PersistentSubscriptionAckEvents(
                $subscriptionId,
                $streamId,
                $processedStreamIds
            ));
        });
    }

    /**
     * @param string $subscriptionId
     * @param string $streamId
     * @param array $processedStreamIds
     * @param string $message
     * @param int $action
     *
     * @return Coroutine\Coroutine
     */
    function fail(string $subscriptionId, string $streamId, array $processedStreamIds, string $message, int $action): Coroutine\Coroutine
    {
        return Coroutine\create(function() use ($subscriptionId, $streamId, $processedStreamIds, $message, $action) {
            yield from $this->transport->handle(new PersistentSubscriptionNakEvents(
                $subscriptionId,
                $streamId,
                $processedStreamIds,
                $message,
                new NakAction($action)
            ));
        });
    }
}
