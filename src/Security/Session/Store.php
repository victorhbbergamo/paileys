<?php

declare(strict_types = 1);

namespace Upward\Paileys\Security\Session;

use Random\RandomException;
use Upward\Paileys\Contracts\Security\KeyInterface;
use Upward\Paileys\Contracts\Security\Session;

/**
 * Implementation of a Double Ratchet encryption session
 */
class Store implements Session
{
    /**
     * The unique identifier for this session
     */
    public protected(set) string $id;

    /**
     * Create a new session with the specified keys
     *
     * @param KeyInterface $rootKey The root key
     * @param KeyInterface $sendingChainKey The sending chain key
     * @param KeyInterface $receivingChainKey The receiving chain key
     * @param bool $isInitiator Whether this session was initiated by the local party
     * @param string|null $id The unique identifier for this session (generated if null)
     * @throws RandomException
     */
    public function __construct(
        public KeyInterface $rootKey,
        public KeyInterface $sendingChainKey,
        public KeyInterface $receivingChainKey,
        protected(set) bool $isInitiator,
        string | null $id = null,
    ) {
        $this->id = $id ?? bin2hex(random_bytes(16)); // Generate a random ID if none provided
    }
}
