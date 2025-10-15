<?php

declare(strict_types=1);

namespace Upward\Paileys\Security\KeyDerivations;

use Upward\Paileys\Contracts\Security\KeyDerivations\Derivation;
use Upward\Paileys\Contracts\Security\KeyInterface;
use Upward\Paileys\Security\Key;

/**
 * HMAC-based Key Derivation Function (HKDF)
 *
 * This implementation follows RFC 5869 and is used in the Signal Protocol
 * for deriving keys from shared secrets.
 */
readonly class HKDF implements Derivation
{
    /**
     * Create a new HKDF instance with the specified hash algorithm
     *
     * @param string $algorithm The hash algorithm to use (default: sha256)
     */
    public function __construct(
        private string $algorithm = 'sha256'
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function deriveKey(KeyInterface $inputKey, string $info = '', int $length = 32): KeyInterface
    {
        // Extract phase: PRK = HMAC-Hash(salt, IKM)
        // We use a zero salt as per Signal Protocol specification
        $salt = str_repeat("\0", $this->getHashLength());
        $prk = hash_hmac($this->algorithm, $inputKey->bytes, $salt, true);

        // Expand phase: OKM = HKDF-Expand(PRK, info, L)
        $okm = $this->expand($prk, $info, $length);

        return new Key($okm);
    }

    /**
     * {@inheritdoc}
     */
    public function deriveKeys(KeyInterface $inputKey, int $numKeys, string $info = '', int $length = 32): array
    {
        $keys = [];

        for ($i = 0; $i < $numKeys; $i++) {
            // Append the key index to the info string to get different keys
            $keyInfo = $info . pack('N', $i);
            $keys[] = $this->deriveKey($inputKey, $keyInfo, $length);
        }

        return $keys;
    }

    /**
     * HKDF-Expand function as defined in RFC 5869
     *
     * @param string $prk The pseudorandom key (from Extract phase)
     * @param string $info The context and application specific information
     * @param int $length The length of the output keying material in bytes
     * @return string The output keying material
     */
    private function expand(string $prk, string $info, int $length): string
    {
        $hashLen = $this->getHashLength();
        $n = ceil($length / $hashLen);
        $okm = '';
        $t = '';

        for ($i = 1; $i <= $n; $i++) {
            $t = hash_hmac($this->algorithm, $t . $info . chr($i), $prk, true);
            $okm .= $t;
        }

        return substr($okm, 0, $length);
    }

    /**
     * Get the output length of the hash function in bytes
     *
     * @return int The hash length in bytes
     */
    private function getHashLength(): int
    {
        return strlen(hash($this->algorithm, '', true));
    }
}
