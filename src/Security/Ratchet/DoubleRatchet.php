<?php

declare(strict_types = 1);

namespace Upward\Paileys\Security\Ratchet;

use Random\RandomException;
use Upward\Paileys\Contracts\Security\Cipher;
use Upward\Paileys\Contracts\Security\Crypto\CryptoFactory;
use Upward\Paileys\Contracts\Security\KeyInterface;
use Upward\Paileys\Contracts\Security\Ratchet\DoubleRatcheting;
use Upward\Paileys\Contracts\Security\Session;
use Upward\Paileys\Security\Session\Store;

/**
 * Implementation of the Double Ratchet algorithm
 *
 * The Double Ratchet algorithm is the core of the Signal Protocol,
 * providing end-to-end encryption with perfect forward secrecy and break-in recovery.
 */
class DoubleRatchet implements DoubleRatcheting
{
    /**
     * The crypto factory used to create cryptographic components
     */
    private CryptoFactory $factory;

    /**
     * The cipher used for encryption/decryption
     */
    private Cipher $cipher;

    /**
     * Create a new Double Ratchet instance
     *
     * @param  CryptoFactory  $factory  The factory to use for creating cryptographic components
     */
    public function __construct(CryptoFactory $factory)
    {
        $this->factory = $factory;
        $this->cipher = $factory->cipher();
    }

    /**
     * {@inheritdoc}
     * @throws RandomException
     */
    public function initializeSession(KeyInterface $sharedSecret, bool $isInitiator): Session
    {
        // Derive the initial root key and chain keys from the shared secret
        $keys = $this->factory->kdf()->deriveKeys($sharedSecret, 3, 'WhatsApp Session Initialization', 32);

        // Create a new session with the derived keys
        return new Store(
            $keys[0], // Root key
            $isInitiator ? $keys[1] : $keys[2], // Sending a chain key
            $isInitiator ? $keys[2] : $keys[1], // Receiving a chain key
            $isInitiator
        );
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt(Session $session, string $plaintext, string $associatedData = ''): string
    {
        // Create a chain from the sending chain key
        $chain = $this->factory->chain($session->sendingChainKey);

        // Get the message key for encryption
        $messageKey = $chain->messageKey;

        // Encrypt the message with the message key
        $ciphertext = $this->cipher->encrypt($plaintext, $messageKey, $associatedData);

        // Advance the chain and update the session's sending chain key
        $nextChain = $chain->nextChain;
        $session->sendingChainKey = $nextChain->key;

        return $ciphertext;
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt(Session $session, string $ciphertext, string $associatedData = ''): string
    {
        // Create a chain from the receiving chain key
        $chain = $this->factory->chain($session->receivingChainKey);

        // Get the message key for decryption
        $messageKey = $chain->messageKey;

        // Decrypt the message with the message key
        $plaintext = $this->cipher->decrypt($ciphertext, $messageKey, $associatedData);

        // Advance the chain and update the session's receiving chain key
        $nextChain = $chain->nextChain;
        $session->receivingChainKey = $nextChain->key;

        return $plaintext;
    }
}
