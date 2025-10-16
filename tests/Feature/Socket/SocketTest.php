<?php

declare(strict_types = 1);

use PHPUnit\Framework\MockObject\Runtime\PropertyHook;
use Upward\Paileys\Contracts\Socket\Client;
use Upward\Paileys\Contracts\Socket\Connection\Manager;
use Upward\Paileys\Contracts\Socket\MessageHandler;
use Upward\Paileys\Socket\Socket;

beforeEach(function () {
    // Create mock objects for testing
    $this->mockClient = Mockery::mock(Client::class);
    $this->mockConnectionManager = $this->createMock(Manager::class);
    $this->mockMessageHandler = Mockery::mock(MessageHandler::class);

    // Create the Socket instance with mock dependencies
    $this->socket = new Socket(
        $this->mockClient,
        $this->mockConnectionManager,
        $this->mockMessageHandler
    );
});

afterEach(function () {
    Mockery::close();
});

it('connects to a WebSocket server', function () {
    // Set up expectations
    $url = 'wss://example.com/socket';
    $this->mockClient->shouldReceive('connect')
        ->once()
        ->with($url)
        ->andReturn(true);

    // Call the method
    $result = $this->socket->connect($url);

    // Assert the result
    expect($result)->toBeTrue();
});

it('disconnects from a WebSocket server', function () {
    // Set up expectations
    $this->mockClient->shouldReceive('disconnect')
        ->once()
        ->andReturn(true);

    // Call the method
    $result = $this->socket->disconnect();

    // Assert the result
    expect($result)->toBeTrue();
});

it('sends a message to the WebSocket server', function () {
    // Set up expectations
    $data = ['message' => 'Hello, server!'];
    $preparedMessage = '{"message":"Hello, server!"}';

    $this->mockMessageHandler->shouldReceive('prepare')
        ->once()
        ->with($data)
        ->andReturn($preparedMessage);

    $this->mockClient->shouldReceive('send')
        ->once()
        ->with($preparedMessage)
        ->andReturn(true);

    // Call the method
    $result = $this->socket->send($data);

    // Assert the result
    expect($result)->toBeTrue();
});

it('registers a message callback', function () {
    // Set up expectations
    $callback = function ($data) {
        // Do something with the data
    };

    $this->mockClient->shouldReceive('onMessage')
        ->once()
        ->with(Mockery::type('Closure'));

    // Call the method
    $this->socket->onMessage($callback);

    // No assertion needed, we're just verifying the method was called
});

it('processes received messages through the message handler', function () {
    // Set up a test message and its processed form
    $rawMessage = '{"type":"message","content":"Hello, client!"}';
    $processedMessage = ['type' => 'message', 'content' => 'Hello, client!'];

    // Set up a flag to track if our callback was called
    $callbackCalled = false;
    $receivedData = null;

    // Set up expectations
    $this->mockMessageHandler->shouldReceive('handle')
        ->once()
        ->with($rawMessage)
        ->andReturn($processedMessage);

    // Set up the client to capture the onMessage callback
    $this->mockClient->shouldReceive('onMessage')
        ->once()
        ->with(Mockery::type('Closure'))
        ->andReturnUsing(function ($callback) use (&$callbackCalled, &$receivedData, $rawMessage) {
            // Call the callback with our test message
            $callback($rawMessage);
            $callbackCalled = true;
        });

    // Register our callback
    $this->socket->onMessage(function ($data) use (&$receivedData) {
        $receivedData = $data;
    });

    // Assert that the callback was called and received the processed message
    expect($callbackCalled)->toBeTrue()
        ->and($receivedData)->toBe($processedMessage);
});

it('checks if the connection is active', function () {
    // Set up expectations
    $this->mockConnectionManager
        ->method(PropertyHook::get('isConnected'))
        ->willReturn(true);

    // Call the method
    $result = $this->socket->connection->isConnected;

    // Assert the result
    expect($result)->toBeTrue();
});

it('attempts to reconnect to the WebSocket server', function () {
    // Set up expectations
    $maxAttempts = 3;
    $delay = 500;

    $this->mockConnectionManager
        ->method('reconnect')
        ->with($maxAttempts, $delay)
        ->willReturn(true);

    // Call the method
    $result = $this->socket->reconnect($maxAttempts, $delay);

    // Assert the result
    expect($result)->toBeTrue();
});

it('returns the connection manager', function () {
    // Call the method
    $result = $this->socket->connection;

    // Assert the result
    expect($result)->toBe($this->mockConnectionManager);
});

it('returns the message handler', function () {
    // Call the method
    $result = $this->socket->messageHandler;

    // Assert the result
    expect($result)->toBe($this->mockMessageHandler);
});
