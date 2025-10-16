<?php

declare(strict_types = 1);

namespace Upward\Paileys\Security\Crypto;

use Upward\Paileys\Contracts\Security\Cipher;
use Upward\Paileys\Contracts\Security\Crypto\CryptoFactory;
use Upward\Paileys\Contracts\Security\KeyDerivations\Derivation;
use Upward\Paileys\Contracts\Security\KeyInterface;
use Upward\Paileys\Contracts\Security\Ratchet\Chaining;
use Upward\Paileys\Security\Cipher\AeadCipher;
use Upward\Paileys\Security\Key;
use Upward\Paileys\Security\KeyDerivations\HKDF;
use Upward\Paileys\Security\Ratchet\Chain;

/**
 * Factory for creating cryptographic components
 */
class Factory implements CryptoFactory
{
    /**
     * Create a new crypto factory with the specified KDF
     *
     * @param Derivation $kdf The KDF to use (default: new HKDF)
     */
    public function __construct(
        public readonly Derivation $kdf = new HKDF(),
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function randomKey(int $size = 32): KeyInterface
    {
        return Key::random($size);
    }

    /**
     * {@inheritdoc}
     */
    public function key(string $bytes): KeyInterface
    {
        return new Key($bytes);
    }

    /**
     * {@inheritdoc}
     */
    public function cipher(): Cipher
    {
        return new AeadCipher();
    }

    /**
     * {@inheritdoc}
     */
    public function kdf(): Derivation
    {
        return $this->kdf;
    }

    /**
     * {@inheritdoc}
     */
    public function chain(KeyInterface $key, int $index = 0): Chaining
    {
        return new Chain($key, $this->kdf, $index);
    }

    /**
     * {@inheritdoc}
     */
    public function shared(string $bytes): KeyInterface
    {
        // For shared secrets, we derive a key using HKDF to ensure it has good properties
        $inputKey = $this->key($bytes);

        return $this->kdf->deriveKey($inputKey, 'WhatsApp Shared Secret', 32);
    }
}
