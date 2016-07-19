<?php

use Mhwk\Ouro\Client\Connection;
use Mhwk\Ouro\Message\EventRecord;
use Icicle\Awaitable;
use Icicle\Coroutine;
use Icicle\Loop;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
require 'vendor/autoload.php';

Coroutine\create(function() {
    $connection = Connection::connect('eventstore:2113', 'admin', 'changeit');

    $connection->readStreamEventsForward('bar', function(EventRecord $record) {
        var_dump($record->getEventNumber());
    });
});

Loop\run();
