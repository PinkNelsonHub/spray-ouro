<?php

namespace Spray\Ouro\Client;

interface IConnectedToEventStore
    extends IWriteToEventStore,
            IWriteToEventStoreAsync,
            IReadFromEventStore,
            IReadFromEventStoreAsync,
            IConnectToPersistentSubscription,
            IConnectToPersistentSubscriptionAsync,
            IConfirmEvent,
            IConfirmEventAsync
{

}
