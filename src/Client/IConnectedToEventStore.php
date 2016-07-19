<?php

namespace Mhwk\Ouro\Client;

interface IConnectedToEventStore
    extends IWriteToEventStore,
            IReadFromEventStore,
            IConnectToPersistentSubscription,
            IConfirmEvent
{

}
