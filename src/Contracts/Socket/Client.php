<?php

declare(strict_types = 1);

namespace Upward\Paileys\Contracts\Socket;

use Closure;

/**
 * Interface for WebSocket client
 *
 * This interface defines the methods needed for WebSocket communication,
 * which is essential for WhatsApp Web functionality.
 */
interface Client
{
    /**
     * Connect to a WebSocket server
     *
     * @param string $url The WebSocket server URL
     * @return bool True if connection was successful, false otherwise
     */
    public function connect(string $url): bool;

    /**
     * Disconnect from the WebSocket server
     *
     * @return bool True if disconnection was successful, false otherwise
     */
    public function disconnect(): bool;

    /**
     * Send a message to the WebSocket server
     *
     * @param string $message The message to send
     * @return bool True if the message was sent successfully, false otherwise
     */
    public function send(string $message): bool;

    /**
     * Register a callback for when a message is received
     *
     * @param Closure $callback The callback function (string $message): void
     * @return void
     */
    public function onMessage(Closure $callback): void;

    /**
     * Register a callback for when the connection is established
     *
     * @param Closure $callback The callback function (): void
     * @return void
     */
    public function onConnect(Closure $callback): void;

    /**
     * Register a callback for when the connection is closed
     *
     * @param Closure $callback The callback function (int $code, string $reason): void
     * @return void
     */
    public function onDisconnect(Closure $callback): void;

    /**
     * Register a callback for when an error occurs
     *
     * @param Closure $callback The callback function (\Exception $e): void
     * @return void
     */
    public function onError(Closure $callback): void;
}
