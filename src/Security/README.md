# Paileys Security Module

This module implements the cryptography layer for the Paileys project, which is a PHP implementation of WhatsApp Web functionality similar to the Baileys project in TypeScript.

## Overview

The Security module provides end-to-end encryption using the Signal Protocol, specifically the Double Ratchet algorithm. This ensures that messages exchanged between users are secure, with properties like perfect forward secrecy and break-in recovery.

## Key Components

### Interfaces

- **KeyInterface**: Interface for cryptographic keys
- **KeyDerivationFunctionInterface**: Interface for key derivation functions
- **CipherInterface**: Interface for symmetric encryption/decryption operations
- **DoubleRatchetInterface**: Interface for the Double Ratchet algorithm
- **SessionInterface**: Interface for Double Ratchet encryption sessions
- **ChainInterface**: Interface for key chains in the Double Ratchet algorithm
- **CryptoFactoryInterface**: Interface for factories that create cryptographic components

### Implementations

- **Key**: Basic implementation of a cryptographic key
- **HKDF**: HMAC-based Key Derivation Function implementation
- **AeadCipher**: Authenticated Encryption with Associated Data using XChaCha20-Poly1305
- **Chain**: Implementation of a key chain for the Double Ratchet algorithm
- **Session**: Implementation of a Double Ratchet encryption session
- **CryptoFactory**: Factory for creating cryptographic components
- **DoubleRatchet**: Implementation of the Double Ratchet algorithm
- **Security**: Main facade class that provides easy access to the cryptography functionality

## Usage

### Basic Usage

```php
// Create a new Security instance
$security = new Security();

// In a real application, the shared secret would be derived from a key exchange
// For example, using X25519 Diffie-Hellman key exchange
$sharedSecret = $security->generateRandomKey();

// Initialize sessions for both parties
$aliceSession = $security->initializeSession($sharedSecret, true); // Alice is the initiator
$bobSession = $security->initializeSession($sharedSecret, false); // Bob is the responder

// Alice encrypts a message for Bob
$plaintext = 'Hello, Bob! This is a secret message.';
$ciphertext = $security->encrypt($aliceSession, $plaintext);

// Bob decrypts the message from Alice
$decrypted = $security->decrypt($bobSession, $ciphertext);
echo $decrypted; // Outputs: Hello, Bob! This is a secret message.

// Bob sends a reply to Alice
$replyPlaintext = 'Hello, Alice! I received your message.';
$replyCiphertext = $security->encrypt($bobSession, $replyPlaintext);

// Alice decrypts Bob's reply
$replyDecrypted = $security->decrypt($aliceSession, $replyCiphertext);
echo $replyDecrypted; // Outputs: Hello, Alice! I received your message.
```

### Using Associated Data

You can include associated data that will be authenticated but not encrypted:

```php
// Alice encrypts a message with associated data
$associatedData = 'Message ID: 12345';
$ciphertext = $security->encrypt($aliceSession, $plaintext, $associatedData);

// Bob must use the same associated data to decrypt
$decrypted = $security->decrypt($bobSession, $ciphertext, $associatedData);
```

## Security Properties

### Perfect Forward Secrecy

The Double Ratchet algorithm provides perfect forward secrecy, which means that even if a session key is compromised, previous messages cannot be decrypted. This is achieved by deriving new keys for each message and advancing the key chain after each operation.

### Break-in Recovery

If an attacker compromises a session key, they will only be able to decrypt messages until the next key rotation. After that, the session will recover and the attacker will no longer be able to decrypt messages.

### Authentication

All messages are authenticated using the Poly1305 message authentication code, which ensures that messages cannot be tampered with without detection.

## Dependencies

- PHP 7.4 or higher
- ext-sodium: The libsodium extension for PHP, which provides modern cryptographic functions

## Testing

The module includes comprehensive tests that demonstrate its functionality and verify its security properties. You can run the tests using PHPUnit:

```bash
vendor/bin/phpunit tests/Feature/Security
```

## License

This module is part of the Paileys project and is licensed under the MIT License.