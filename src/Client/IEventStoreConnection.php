<?php

namespace Mhwk\Ouro\Client;

use Mhwk\Ouro\Async\IAction;
use Mhwk\Ouro\Async\ITask;

interface IEventStoreConnection
{
    function getConnectionName(): string;

    function connectAsync(): ITask;

    function close();

    function appendToStreamAsync(string $stream, int $expectedVersion, ...$events): ITask;

    function readStreamEventsForwardAsync(string $stream, int $start, int $count, bool $resolveLinkTos): ITask;

    function connectToPersistentSubscription(
        string $stream,
        string $groupName,
        IAction $eventAppeared,
        IAction $subscriptionDropped,
        int $bufferSize,
        bool $autoAck = true): EventStorePersistentSubscriptionBase;

    function connectToPersistentSubscriptionAsync(
        string $stream,
        string $groupName,
        IAction $eventAppeared,
        IAction $subscriptionDropped,
        int $bufferSize,
        bool $autoAck = true): ITask;
}
