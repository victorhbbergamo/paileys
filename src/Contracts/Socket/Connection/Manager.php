<?php

declare(strict_types = 1);

namespace Upward\Paileys\Contracts\Socket\Connection;

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
     * Get the current connection state
     *
     * @return State The current connection state (e.g., "connected", "disconnected", "connecting")
     */
    public function getState(): State;

    /**
     * Check if the connection is currently active
     *
     * @return bool True if connected, false otherwise
     */
    public function isConnected(): bool;

    /**
     * Attempt to reconnect to the WebSocket server
     *
     * @param int $maxAttempts Maximum number of reconnection attempts
     * @param int $delay Delay between reconnection attempts in milliseconds
     * @return bool True if reconnection was successful, false otherwise
     */
    public function reconnect(int $maxAttempts = 5, int $delay = 1000): bool;

    /**
     * Set the reconnection strategy
     *
     * @param callable $strategy The reconnection strategy function (int $attempt): int
     *                          Should return the delay in milliseconds before the next attempt
     * @return void
     */
    public function setReconnectionStrategy(callable $strategy): void;

    /**
     * Get the time the connection was established
     *
     * @return int|null The timestamp when the connection was established, or null if not connected
     */
    public function getConnectedSince(): int | null;

    /**
     * Get the URL of the WebSocket server
     *
     * @return string|null The WebSocket server URL, or null if not connected
     */
    public function getServerUrl(): string | null;
}
