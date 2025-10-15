<?php

declare(strict_types = 1);

namespace Upward\Paileys\Contracts\Security\KeyDerivations;

use Upward\Paileys\Contracts\Security\KeyInterface;

/**
 * Interface for key derivation functions
 *
 * Key derivation functions are used to derive new keys from existing ones,
 * which is a core component of the Double Ratchet algorithm.
 */
interface Derivation
{
    /**
     * Derive a new key from an input key and optional context information
     *
     * @param  KeyInterface  $inputKey  The input key material
     * @param  string  $info  Optional context and application specific information
     * @param  int  $length  The desired length of the derived key in bytes
     * @return KeyInterface The derived key
     */
    public function deriveKey(KeyInterface $inputKey, string $info = '', int $length = 32): KeyInterface;

    /**
     * Derive multiple keys from an input key
     *
     * @param  KeyInterface  $inputKey  The input key material
     * @param  int  $numKeys  The number of keys to derive
     * @param  string  $info  Optional context and application specific information
     * @param  int  $length  The desired length of each derived key in bytes
     * @return array<KeyInterface> An array of derived keys
     */
    public function deriveKeys(KeyInterface $inputKey, int $numKeys, string $info = '', int $length = 32): array;
}
