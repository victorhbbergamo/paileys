<?php

declare(strict_types = 1);

namespace Upward\Paileys\Security;

use Exception;
use Random\RandomException;
use Upward\Paileys\Contracts\Security\Crypto\CryptoFactory;
use Upward\Paileys\Contracts\Security\KeyInterface;
use Upward\Paileys\Contracts\Security\Session;
use Upward\Paileys\Security\Crypto\Factory;
use Upward\Paileys\Security\Ratchet\DoubleRatchet;

/**
 * Main security class that provides a facade for the cryptography layer
 *
 * This class serves as the main entry point for the security functionality,
 * making it easier for other parts of the application to use the cryptography
 * features without having to interact with the individual components directly.
 */
class Security
{
    /**
     * The Double Ratchet algorithm implementation
     */
    private DoubleRatchet $doubleRatchet;

    /**
     * Create a new Security instance
     *
     * @param CryptoFactory $factory The factory to use for creating cryptographic components
     */
    public function __construct(
        public readonly CryptoFactory $factory = new Factory(),
    ) {
        $this->doubleRatchet = new DoubleRatchet($this->factory);
    }

    /**
     * Generate a random key of the specified size
     *
     * @param int $size The size of the key in bytes
     * @return KeyInterface The generated random key
     */
    public function generateRandomKey(int $size = 32): KeyInterface
    {
        return $this->factory->randomKey($size);
    }

    /**
     * Create a key from existing bytes
     *
     * @param string $bytes The raw key bytes
     * @return KeyInterface The key object
     */
    public function createKey(string $bytes): KeyInterface
    {
        return $this->factory->key($bytes);
    }

    /**
     * Create a shared key from the provided key material
     *
     * @param string $bytes The shared secret bytes
     * @return KeyInterface The shared key
     */
    public function createSharedKey(string $bytes): KeyInterface
    {
        return $this->factory->shared($bytes);
    }

    /**
     * Initialize a new session for communication
     *
     * @param KeyInterface $sharedSecret The initial shared secret key
     * @param bool $isInitiator Whether this party is the initiator of the session
     * @return Session The initialized session
     * @throws RandomException
     */
    public function initializeSession(KeyInterface $sharedSecret, bool $isInitiator): Session
    {
        return $this->doubleRatchet->initializeSession($sharedSecret, $isInitiator);
    }

    /**
     * Encrypt a message using the Double Ratchet algorithm
     *
     * @param Session $session The session to use for encryption
     * @param string $plaintext The plaintext message to encrypt
     * @param string $associatedData Optional associated data to authenticate
     * @return string The encrypted message
     */
    public function encrypt(Session $session, string $plaintext, string $associatedData = ''): string
    {
        return $this->doubleRatchet->encrypt($session, $plaintext, $associatedData);
    }

    /**
     * Decrypt a message using the Double Ratchet algorithm
     *
     * @param Session $session The session to use for decryption
     * @param string $ciphertext The encrypted message to decrypt
     * @param string $associatedData Optional associated data to authenticate
     * @return string The decrypted plaintext message
     *
     * @throws Exception If decryption fails
     */
    public function decrypt(Session $session, string $ciphertext, string $associatedData = ''): string
    {
        return $this->doubleRatchet->decrypt($session, $ciphertext, $associatedData);
    }
}
