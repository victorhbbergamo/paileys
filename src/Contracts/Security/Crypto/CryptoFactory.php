<?php

declare(strict_types = 1);

namespace Upward\Paileys\Contracts\Security\Crypto;

use Upward\Paileys\Contracts\Security\Cipher;
use Upward\Paileys\Contracts\Security\KeyDerivations\Derivation;
use Upward\Paileys\Contracts\Security\KeyInterface;
use Upward\Paileys\Contracts\Security\Ratchet\Chaining;

/**
 * Interface for factories that create cryptographic components
 *
 * This factory is responsible for creating keys, ciphers, KDFs, and other
 * cryptographic components needed for the Double Ratchet algorithm.
 */
interface CryptoFactory
{
    /**
     * Create a random key of the specified size
     *
     * @param  int  $size  The size of the key in bytes
     * @return KeyInterface The generated random key
     */
    public function randomKey(int $size = 32): KeyInterface;

    /**
     * Create a key from existing bytes
     *
     * @param  string  $bytes  The raw key bytes
     * @return KeyInterface The key object
     */
    public function key(string $bytes): KeyInterface;

    /**
     * Create a cipher for encryption/decryption
     *
     * @return Cipher The cipher implementation
     */
    public function cipher(): Cipher;

    /**
     * Create a key derivation function
     *
     * @return Derivation The KDF implementation
     */
    public function kdf(): Derivation;

    /**
     * Create a key chain with the specified key
     *
     * @param  KeyInterface  $key  The key to use for the chain
     * @param  int  $index  The index of this key in the chain sequence
     * @return Chaining The chain implementation
     */
    public function chain(KeyInterface $key, int $index = 0): Chaining;

    /**
     * Create a shared key from the provided key material
     *
     * This is typically used to create a key from the output of a key agreement protocol
     *
     * @param  string  $bytes  The shared secret bytes
     * @return KeyInterface The shared key
     */
    public function shared(string $bytes): KeyInterface;
}
