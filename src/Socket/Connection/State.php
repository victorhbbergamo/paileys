<?php

declare(strict_types = 1);

namespace Upward\Paileys\Socket\Connection;

/**
 * Possible connection states
 */
enum State: string
{
    case Disconnected = 'disconnected';

    case Connecting = 'connecting';

    case Connected = 'connected';

    case Reconnecting = 'reconnecting';
}
