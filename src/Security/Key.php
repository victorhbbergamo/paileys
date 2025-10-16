<?php

declare(strict_types = 1);

namespace Upward\Paileys\Security;

use Random\RandomException;
use Upward\Paileys\Contracts\Security\KeyInterface;

/**
 * Basic implementation of a cryptographic key
 */
class Key implements KeyInterface
{
    public int $length {
        get => strlen($this->bytes);
    }

    /**
     * Create a new key with the specified bytes
     *
     * @param string $bytes The raw key bytes
     */
    public function __construct(
        public readonly string $bytes,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function equals(KeyInterface $other): bool
    {
        // Use hash_equals for constant-time comparison to prevent timing attacks
        return hash_equals($this->bytes, $other->bytes);
    }

    /**
     * Create a new key with random bytes of the specified size
     *
     * @param int $size The size of the key in bytes
     * @return self The generated random key
     * @throws RandomException
     */
    public static function random(int $size = 32): self
    {
        return new self(random_bytes($size));
    }

    /**
     * Create a new key from the provided bytes
     *
     * @param string $bytes The raw key bytes
     * @return self The key object
     */
    public static function fromBytes(string $bytes): self
    {
        return new self($bytes);
    }
}
