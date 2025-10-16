<?php

declare(strict_types = 1);

namespace Upward\Paileys\Contracts\Security;

/**
 * Interface for cryptographic keys
 */
interface KeyInterface
{
    /**
     * Get the raw bytes of the key
     */
    public string $bytes {
        get;
    }

    /**
     * Get the key size in bytes
     */
    public int $length {
        get;
    }

    /**
     * Check if this key is equal to another key
     *
     * @param KeyInterface $other The other key to compare with
     * @return bool True if the keys are equal, false otherwise
     */
    public function equals(KeyInterface $other): bool;
}
