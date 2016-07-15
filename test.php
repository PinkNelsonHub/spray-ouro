<?php

use Mhwk\Ouro\Message\ConnectToPersistentSubscription;
use Mhwk\Ouro\Message\NakAction;
use Mhwk\Ouro\Message\NewEvent;
use Mhwk\Ouro\Message\PersistentSubsciptionStreamEventAppeared;
use Mhwk\Ouro\Message\PersistentSubscriptionAckEvents;
use Mhwk\Ouro\Message\PersistentSubscriptionNakEvents;
use Mhwk\Ouro\Message\ReadStreamEventsComplete;
use Mhwk\Ouro\Message\ReadStreamEventsForward;
use Mhwk\Ouro\Message\WriteEvents;
use Mhwk\Ouro\Transport\Http\HttpTransport;
use Ramsey\Uuid\Uuid;

error_reporting(-1);
ini_set('display_errors', 1);

chdir(__DIR__);
require 'vendor/autoload.php';

$client = HttpTransport::factory('http://eventstore.dev:80', 'admin', 'changeit');

$time = microtime(true);
var_dump($client->handle(new WriteEvents('foo', -2, [
    new NewEvent(Uuid::uuid4(), 'Foo', ['foo' => 'bar'], ['fqn' => 'Bar\\Foo'])
], false)));
var_dump(microtime(true) - $time);

$time = microtime(true);
$stream = $client->handle(new ReadStreamEventsForward('foo', 0, 100, false));
/** @var ReadStreamEventsComplete $complete */
foreach ($stream as $complete) {
    var_dump(get_class($complete));
}
var_dump(microtime(true) - $time);

$time = microtime(true);
$subscription = $client->handle(new ConnectToPersistentSubscription('saan', 'foo', 5));
/** @var PersistentSubsciptionStreamEventAppeared $appeared */
foreach ($subscription as $appeared) {
    var_dump($appeared);

    if ((bool) rand(0, 1)) {
        $subscription->send(new PersistentSubscriptionAckEvents('saan', [$appeared->getEvent()->getEvent()->getEventId()]));
    } else {
        $subscription->send(new PersistentSubscriptionNakEvents('saan', [$appeared->getEvent()->getEvent()->getEventId()], 'Something happened', NakAction::park()));
    }

}
var_dump(microtime(true) - $time);
