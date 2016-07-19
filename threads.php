<?php

use Icicle\Awaitable;
use Icicle\Coroutine;
use Icicle\Loop;
use Mhwk\Ouro\Client\PersistentSubscription;
use Mhwk\Ouro\Message\NewEvent;
use Mhwk\Ouro\Message\PersistentSubsciptionStreamEventAppeared;
use Mhwk\Ouro\Message\WriteEvents;
use Mhwk\Ouro\Transport\Http\HttpTransport;
use Ramsey\Uuid\Uuid;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
require 'vendor/autoload.php';

$transport = HttpTransport::factory('eventstore:2113', 'admin', 'changeit');
$transport->handle(new WriteEvents(
    'foo',
    -2,
    [
        new NewEvent(
            Uuid::uuid4(),
            'Foo',
            ['foo'=>'bar'],
            []
        )
    ],
    false
));

$subscriptions = new PersistentSubscription($transport);

$coroutine = Coroutine\create(function() use ($subscriptions) {
    yield from $subscriptions->connect('bar', 'foo', 10, function(PersistentSubsciptionStreamEventAppeared $appeared) use ($subscriptions) {
        echo "Appeared: {$appeared->getEvent()->getEvent()->getEventType()}: {$appeared->getEvent()->getEvent()->getEventId()}\n";
    });
});
$coroutine->then(
    function() {},
    function(Exception $e) {
        echo $e->getMessage() . "\n";
    }
);

$timeout = Coroutine\create(function() use ($coroutine) {
    yield Awaitable\resolve()->delay(5);
    $coroutine->cancel();
});

Loop\run();
