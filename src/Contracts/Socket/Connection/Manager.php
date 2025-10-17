<?php

declare(strict_types = 1);

namespace Upward\Paileys\Contracts\Socket\Connection;

use Closure;
use Upward\Paileys\Socket\Connection\State;

/**
 * Interface for managing WebSocket connections
 *
 * This interface defines the methods needed for managing WebSocket connections,
 * including reconnection logic and connection state.
 */
interface Manager
{
    /**
     * Check if the connection is currently active
     */
    public bool $isConnected {
        get;
    }

    /**
     * The timestamp when the connection was established, or null if not connected
     */
    public protected(set) int | null $connectedSince {
        get;
        set;
    }

    /**
     * Get the URL of the WebSocket server
     *
     * @return string|null The WebSocket server URL, or null if not connected
     */
    public protected(set) null | string $serverUrl {
        get;
        set;
    }

    /**
     * Get the current connection state
     *
     * @return State The current connection state (e.g., "connected", "disconnected", "connecting")
     */
    public protected(set) State $state {
        get;
        set;
    }

    /**
     * The reconnection strategy function (int $attempt): int
     * Should return the delay in milliseconds before the next attempt
     *
     * @var Closure(int $attempts): int $reconnectionStrategy
     */
    public Closure $reconnectionStrategy {
        set;
    }

    /**
     * Attempt to reconnect to the WebSocket server
     *
     * @param int $maxAttempts Maximum number of reconnection attempts
     * @param int $delay Delay between reconnection attempts in milliseconds
     * @return bool True if reconnection was successful, false otherwise
     */
    public function reconnect(int $maxAttempts = 5, int $delay = 1000): bool;
}
