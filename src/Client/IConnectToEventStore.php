<?php

namespace Mhwk\Ouro\Client;

interface IConnectToEventStore
{
    static function connect(string $host, string $username, string $password): IConnectedToEventStore;
}
