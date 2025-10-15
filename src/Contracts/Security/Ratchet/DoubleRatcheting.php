<?php

declare(strict_types = 1);

namespace Upward\Paileys\Contracts\Security\Ratchet;

use Exception;
use Upward\Paileys\Contracts\Security\KeyInterface;
use Upward\Paileys\Contracts\Security\Session;

/**
 * Interface for the Double Ratchet algorithm
 *
 * The Double Ratchet algorithm is the core of the Signal Protocol,
 * providing end-to-end encryption with perfect forward secrecy and break-in recovery.
 */
interface DoubleRatcheting
{
    /**
     * Initialize a new session for communication
     *
     * @param  KeyInterface  $sharedSecret  The initial shared secret key
     * @param  bool  $isInitiator  Whether this party is the initiator of the session
     * @return Session The initialized session
     */
    public function initializeSession(KeyInterface $sharedSecret, bool $isInitiator): Session;

    /**
     * Encrypt a message using the Double Ratchet algorithm
     *
     * @param  Session  $session  The session to use for encryption
     * @param  string  $plaintext  The plaintext message to encrypt
     * @param  string  $associatedData  Optional associated data to authenticate
     * @return string The encrypted message
     */
    public function encrypt(Session $session, string $plaintext, string $associatedData = ''): string;

    /**
     * Decrypt a message using the Double Ratchet algorithm
     *
     * @param  Session  $session  The session to use for decryption
     * @param  string  $ciphertext  The encrypted message to decrypt
     * @param  string  $associatedData  Optional associated data to authenticate
     * @return string The decrypted plaintext message
     *
     * @throws Exception If decryption fails
     */
    public function decrypt(Session $session, string $ciphertext, string $associatedData = ''): string;
}
