<?php

declare(strict_types=1);

namespace Upward\Paileys\Contracts\Security\Ratchet;

use Upward\Paileys\Contracts\Security\KeyInterface;

/**
 * Interface for keychains in the Double Ratchet algorithm
 *
 * A chain is a sequence of keys derived from a single root key.
 * Each step in the chain produces a new chain key and a message key.
 */
interface Chaining
{
    /**
     * The current chain key
     */
    public KeyInterface $key {
        get;
    }

    /**
     * Get the index of this chain key in the sequence (starting from 0 for the initial key)
     */
    public int $index {
        get;
    }

    /**
     * Get a message key derived from the current chain key
     *
     * Message keys are used for encrypting/decrypting individual messages
     */
    protected(set) KeyInterface $messageKey {
        get;
        set;
    }

    /**
     * Advance the chain to the next key
     *
     * This creates a new chain with the next chain key in the sequence
     */
    protected(set) Chaining $nextChain {
        get;
        set;
    }

    /**
     * Create a new chain with a specific key
     *
     * @param KeyInterface $key The key to use for the new chain
     * @return Chaining A new chain with the specified key
     */
    public function withKey(KeyInterface $key): Chaining;
}
