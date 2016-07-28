<?php

use Icicle\Coroutine;
use Icicle\Loop;
use Spray\Ouro\Client\Connection;
use Spray\Ouro\Client\IConnectToPersistentSubscriptionAsync;
use Spray\Ouro\Transport\Message\EventRecord;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
require 'vendor/autoload.php';

Coroutine\create(function() {
    /** @var IConnectToPersistentSubscriptionAsync $connection */
    $connection = Connection::connect('eventstore:2113', 'admin', 'changeit');
    yield $connection->subscribePersistentAsync('saanka', 'bar', 50, function(EventRecord $record) {
        var_dump($record->getEventNumber());
    });
});

Loop\run();
