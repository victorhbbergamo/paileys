<?php

declare(strict_types = 1);

namespace Upward\Paileys\Security\Ratchet;

use Upward\Paileys\Contracts\Security\KeyDerivations\Derivation;
use Upward\Paileys\Contracts\Security\KeyInterface;
use Upward\Paileys\Contracts\Security\Ratchet\Chaining;

/**
 * Implementation of a keychain for the Double Ratchet algorithm
 */
class Chain implements Chaining
{
    /**
     * Create a new chain with the specified key and KDF
     *
     * @param KeyInterface $key The key to use for the chain
     * @param Derivation $kdf The KDF to use for key derivation
     * @param int $index The index of this key in the chain sequence
     */
    public function __construct(
        public readonly KeyInterface $key,
        protected readonly Derivation $kdf,
        public readonly int $index = 0,
    ) {
    }

    public protected(set) KeyInterface $messageKey {
        get => $this->getMessageKey();
        set => $this->messageKey = $value;
    }

    public protected(set) Chaining $nextChain {
        get => $this->getNextChainKey();
        set => $this->nextChain = $value;
    }

    /**
     * Get a message key derived from the current chain key
     *
     * Message keys are used for encrypting/decrypting individual messages
     */
    private function getMessageKey(): KeyInterface
    {
        // Derive a message key from the chain key
        // Use different info strings for chain key and message key to ensure they're different
        return $this->kdf->deriveKey($this->key, 'WhatsApp Message Key', 32);
    }

    private function getNextChainKey(): Chaining
    {
        // Derive the next chain key from the current one
        $nextKey = $this->kdf->deriveKey($this->key, 'WhatsApp Chain Key', 32);

        // Create a new chain with the next key and an incremented index
        return new self($nextKey, $this->kdf, $this->index + 1);
    }

    /**
     * {@inheritdoc}
     */
    public function withKey(KeyInterface $key): Chaining
    {
        // Create a new chain with the specified key but keep the same index
        return new self($key, $this->kdf, $this->index);
    }
}
