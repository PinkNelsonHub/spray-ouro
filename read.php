<?php

use Spray\Ouro\Client\Connection;
use Spray\Ouro\Transport\Message\EventRecord;
use Icicle\Awaitable;
use Icicle\Coroutine;
use Icicle\Loop;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
require 'vendor/autoload.php';

$coroutine = Coroutine\create(function() {
    $connection = Connection::connect('eventstore:2113', 'admin', 'changeit');

    yield $connection->readStreamEventsForwardAsync('bar', function(EventRecord $record) {
        var_dump($record->getEventNumber());
    });
});
$coroutine->capture(function(Throwable $e) {
    echo sprintf(
        "%s on line %s in file %s\n%s\n",
        $e->getMessage(),
        $e->getLine(),
        $e->getFile(),
        $e->getTraceAsString()
    );
});

Loop\run();
