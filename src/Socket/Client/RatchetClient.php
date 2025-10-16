<?php

declare(strict_types = 1);

namespace Upward\Paileys\Socket\Client;

use Closure;
use Exception;
use Ratchet\Client\Connector;
use Ratchet\Client\WebSocket as RatchetWebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\Loop;
use React\Promise\PromiseInterface;
use Throwable;
use Upward\Paileys\Contracts\Socket\Client;

/**
 * WebSocket client implementation using Ratchet/Pawl
 */
class RatchetClient implements Client
{
    /**
     * The WebSocket connection
     */
    private RatchetWebSocket | null $connection = null;

    /**
     * The message received callback
     */
    private Closure | null $messageCallback = null;

    /**
     * The connection established callback
     */
    private Closure | null $connectCallback = null;

    /**
     * The connection closed callback
     */
    private Closure | null $disconnectCallback = null;

    /**
     * The error callback
     */
    private Closure | null $errorCallback = null;

    /**
     * {@inheritdoc}
     */
    public function connect(string $url): bool
    {
        try {
            $connector = new Connector(Loop::get());

            /** @var PromiseInterface<RatchetWebSocket> $promise */
            $promise = $connector($url);

            $promise->then(
                onFulfilled: function (RatchetWebSocket $conn): void {
                    $this->connection = $conn;

                    // Set up a message handler
                    $conn->on('message', function (MessageInterface $msg) {
                        if ($this->messageCallback !== null) {
                            ($this->messageCallback)((string) $msg);
                        }
                    });

                    // Set up close handler
                    $conn->on('close', function ($code = null, $reason = null) {
                        $this->connection = null;

                        if ($this->disconnectCallback !== null) {
                            ($this->disconnectCallback)($code ?? 0, $reason ?? '');
                        }
                    });

                    // Call the connect callback if set
                    if ($this->connectCallback !== null) {
                        ($this->connectCallback)();
                    }
                },
                onRejected: function (Throwable $exception): void {
                    if ($this->errorCallback !== null) {
                        ($this->errorCallback)($exception);
                    }
                }
            );

            return true;
        } catch (Exception $e) {
            if ($this->errorCallback !== null) {
                ($this->errorCallback)($e);
            }

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(): bool
    {
        if ($this->connection === null) {
            return false;
        }

        try {
            $this->connection->close();
            $this->connection = null;

            return true;
        } catch (Exception $e) {
            if ($this->errorCallback !== null) {
                ($this->errorCallback)($e);
            }

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $message): bool
    {
        if ($this->connection === null) {
            return false;
        }

        try {
            $this->connection->send($message);

            return true;
        } catch (Exception $e) {
            if ($this->errorCallback !== null) {
                ($this->errorCallback)($e);
            }

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage(Closure $callback): void
    {
        $this->messageCallback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function onConnect(Closure $callback): void
    {
        $this->connectCallback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function onDisconnect(Closure $callback): void
    {
        $this->disconnectCallback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function onError(Closure $callback): void
    {
        $this->errorCallback = $callback;
    }
}
