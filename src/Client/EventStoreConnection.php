<?php

namespace Mhwk\Ouro\Client;

final class EventStoreConnection
{
    public static function create(string $uri, string $connectionName = null): IEventStoreConnection
    {
        return new EventStoreNodeConnection(

        );
    }
}
