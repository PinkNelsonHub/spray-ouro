<?php

use Icicle\Coroutine;
use Icicle\Loop;
use Mhwk\Ouro\Client\Connection;
use Mhwk\Ouro\Client\IConnectToPersistentSubscription;
use Mhwk\Ouro\Message\EventRecord;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
require 'vendor/autoload.php';

Coroutine\create(function() {
    /** @var IConnectToPersistentSubscription $connection */
    $connection = Connection::connect('eventstore:2113', 'admin', 'changeit');
    yield $connection->subscribePersistent('saanka', 'bar', 50, function(EventRecord $record) {
        var_dump($record->getEventNumber());
    });
});

Loop\run();
