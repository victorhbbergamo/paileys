<?php

declare(strict_types = 1);

namespace Upward\Paileys\Socket;

use Closure;
use Exception;
use Upward\Paileys\Contracts\Socket\Client;
use Upward\Paileys\Contracts\Socket\Connection\Manager;
use Upward\Paileys\Socket\Connection\State;

/**
 * Default implementation of the ConnectionManager interface
 */
class ConnectionManager implements Manager
{
    /**
     * Current connection state
     */
    protected(set) State $state = State::Disconnected;

    /**
     * The WebSocket server URL
     */
    protected(set) string | null $serverUrl = null;

    /**
     * The time the connection was established
     */
    protected(set) int | null $connectedSince = null;

    /**
     * The reconnection strategy
     */
    public Closure $reconnectionStrategy {
        set(callable $value) {
            $this->reconnectionStrategy = $value;
        }
    }

    public bool $isConnected {
        get => $this->state === State::Connected;
    }

    /**
     * Create a new connection manager
     *
     * @param Client $client The WebSocket client to use
     */
    public function __construct(
        protected readonly Client $client,
    ) {
        // Set the default reconnection strategy (exponential backoff)
        $this->reconnectionStrategy = function (int $attempt): int {
            return min(30000, 1000 * (2 ** $attempt)); // Max 30 seconds
        };

        // Set up event handlers
        $this->setupEventHandlers();
    }

    /**
     * {@inheritdoc}
     */
    public function reconnect(int $maxAttempts = 5, int $delay = 1000): bool
    {
        if ($this->state === State::Connected) {
            return true;
        }

        $this->state = State::Reconnecting;

        $attempt = 0;
        $connected = false;

        while ($attempt < $maxAttempts && ! $connected) {
            $attempt++;

            // Calculate delay based on the reconnection strategy
            $currentDelay = ($this->reconnectionStrategy)($attempt - 1);

            // Wait before attempting to reconnect
            usleep($currentDelay * 1000); // Convert to microseconds

            // Attempt to connect
            if ($this->serverUrl !== null) {
                $connected = $this->client->connect($this->serverUrl);

                if ($connected) {
                    $this->state = State::Connected;
                    $this->connectedSince = time();

                    return true;
                }
            }
        }

        $this->state = State::Disconnected;

        return false;
    }

    /**
     * Set up event handlers for the WebSocket client
     */
    private function setupEventHandlers(): void
    {
        // Handle connection established
        $this->client->onConnect(function (): void {
            $this->state = State::Connected;
            $this->connectedSince = time();
        });

        // Handle connection closed
        $this->client->onDisconnect(function (int $code, string $reason): void {
            $this->state = State::Disconnected;
            $this->connectedSince = null;
        });

        // Handle errors
        $this->client->onError(function (Exception $e): void {
            // If we were connecting, update the state to disconnected
            if ($this->state === State::Connecting || $this->state === State::Reconnecting) {
                $this->state = State::Disconnected;
            }
        });
    }
}
