<?php

declare(strict_types = 1);

namespace Upward\Paileys\Contracts\Socket;

/**
 * Interface for handling WebSocket messages
 *
 * This interface defines the methods needed for processing messages
 * received from the WebSocket server.
 */
interface MessageHandler
{
    /**
     * Process a message received from the WebSocket server
     *
     * @param string $message The raw message received
     * @return mixed The processed message or response
     */
    public function handle(string $message): mixed;

    /**
     * Prepare a message to be sent to the WebSocket server
     *
     * @param mixed $data The data to be sent
     * @return string The prepared message
     */
    public function prepare(mixed $data): string;
}
