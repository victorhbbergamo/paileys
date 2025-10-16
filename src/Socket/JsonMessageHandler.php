<?php

declare(strict_types = 1);

namespace Upward\Paileys\Socket;

use InvalidArgumentException;
use JsonException;
use Upward\Paileys\Contracts\Socket\MessageHandler;

/**
 * JSON message handler for WebSocket communication
 *
 * This handler processes JSON messages received from the WebSocket server
 * and prepares JSON messages to be sent to the server.
 */
class JsonMessageHandler implements MessageHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(string $message): mixed
    {
        try {
            // Decode the JSON message
            $data = json_decode($message, true, 512, JSON_THROW_ON_ERROR);

            return $data;
        } catch (JsonException $e) {
            // If the message is not valid JSON, return it as-is
            return $message;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(mixed $data): string
    {
        if (is_string($data)) {
            // Check if the string is already a valid JSON
            try {
                json_decode($data, true, 512, JSON_THROW_ON_ERROR);

                return $data; // It's already a valid JSON string
            } catch (JsonException) {
                // Not a JSON string, encode it as a JSON string
                return json_encode($data, JSON_THROW_ON_ERROR);
            }
        }

        if (is_array($data) || is_object($data)) {
            // Encode arrays and objects as JSON
            try {
                return json_encode($data, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new InvalidArgumentException('Failed to encode data as JSON: ' . $e->getMessage(), 0, $e);
            }
        }

        // For scalar values, encode them as JSON
        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
