<?php

declare(strict_types = 1);

namespace Upward\Paileys\Socket;

use Closure;
use Upward\Paileys\Contracts\Socket\Client;
use Upward\Paileys\Contracts\Socket\Connection\Manager;
use Upward\Paileys\Contracts\Socket\MessageHandler;
use Upward\Paileys\Socket\Client\RatchetClient;

/**
 * Main socket class that provides a facade for the WebSocket communication layer
 *
 * This class serves as the main entry point for the WebSocket functionality,
 * making it easier for other parts of the application to use WebSocket
 * features without having to interact with the individual components directly.
 */
class Socket
{
    /**
     * The connection manager
     */
    public readonly Manager $connection;

    /**
     * Check if the connection is currently active
     */
    public bool $isConnected {
        get => $this->connection->isConnected();
    }

    /**
     * Create a new Socket instance
     *
     * @param Client $client The WebSocket client to use
     * @param Manager|null $connection The connection manager to use
     * @param MessageHandler $messageHandler The message handler to use
     */
    public function __construct(
        public readonly Client $client = new RatchetClient(),
        Manager | null $connection = null,
        public readonly MessageHandler $messageHandler = new JsonMessageHandler(),
    ) {
        $this->connection = $connection ?? new ConnectionManager($client);
    }

    /**
     * Connect to a WebSocket server
     *
     * @param string $url The WebSocket server URL
     * @return bool True if connection was successful, false otherwise
     */
    public function connect(string $url): bool
    {
        return $this->client->connect($url);
    }

    /**
     * Disconnect from the WebSocket server
     *
     * @return bool True if disconnection was successful, false otherwise
     */
    public function disconnect(): bool
    {
        return $this->client->disconnect();
    }

    /**
     * Send a message to the WebSocket server
     *
     * @param mixed $data The data to send
     * @return bool True if the message was sent successfully, false otherwise
     */
    public function send(mixed $data): bool
    {
        // Prepare the message using the message handler
        $message = $this->messageHandler->prepare($data);

        // Send the prepared message
        return $this->client->send($message);
    }

    /**
     * Register a callback for when a message is received
     *
     * @param Closure $callback The callback function (mixed $data): void
     * @return void
     */
    public function onMessage(Closure $callback): void
    {
        // Register a callback that processes the message using the message handler
        $this->client->onMessage(function (string $message) use ($callback): void {
            $data = $this->messageHandler->handle($message);
            $callback($data);
        });
    }

    /**
     * Register a callback for when the connection is established
     *
     * @param Closure $callback The callback function (): void
     * @return void
     */
    public function onConnect(Closure $callback): void
    {
        $this->client->onConnect($callback);
    }

    /**
     * Register a callback for when the connection is closed
     *
     * @param Closure $callback The callback function (int $code, string $reason): void
     * @return void
     */
    public function onDisconnect(Closure $callback): void
    {
        $this->client->onDisconnect($callback);
    }

    /**
     * Register a callback for when an error occurs
     *
     * @param Closure $callback The callback function (\Exception $e): void
     * @return void
     */
    public function onError(Closure $callback): void
    {
        $this->client->onError($callback);
    }

    /**
     * Attempt to reconnect to the WebSocket server
     *
     * @param int $maxAttempts Maximum number of reconnection attempts
     * @param int $delay Delay between reconnection attempts in milliseconds
     * @return bool True if reconnection was successful, false otherwise
     */
    public function reconnect(int $maxAttempts = 5, int $delay = 1000): bool
    {
        return $this->connection->reconnect($maxAttempts, $delay);
    }
}
