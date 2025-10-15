<?php

declare(strict_types = 1);

namespace Upward\Paileys\Contracts\Security;

/**
 * Interface for Double Ratchet encryption sessions
 *
 * A session maintains the state of an ongoing encrypted conversation,
 * including the chain keys and other parameters needed for the Double Ratchet algorithm.
 */
interface Session
{
    /**
     * Get and Set the root key for this session
     */
    public KeyInterface $rootKey {
        set;
    }

    public KeyInterface $sendingChainKey {
        set;
    }

    public KeyInterface $receivingChainKey {
        set;
    }

    protected(set) bool $isInitiator {
        get;
        set;
    }

    protected(set) string $id {
        get;
        set;
    }
}
