<?php

declare(strict_types = 1);

namespace Upward\Paileys\Contracts\Security;

use Exception;

/**
 * Interface for symmetric encryption/decryption operations
 *
 * This interface defines the methods needed for symmetric encryption,
 * which is used in the Double Ratchet algorithm to encrypt and decrypt messages.
 */
interface Cipher
{
    /**
     * Encrypt a plaintext message using a key
     *
     * @param  string  $plaintext  The plaintext message to encrypt
     * @param  KeyInterface  $key  The encryption key
     * @param  string  $associatedData  Optional associated data that will be authenticated but not encrypted
     * @return string The encrypted ciphertext
     */
    public function encrypt(string $plaintext, KeyInterface $key, string $associatedData = ''): string;

    /**
     * Decrypt a ciphertext message using a key
     *
     * @param  string  $ciphertext  The ciphertext message to decrypt
     * @param  KeyInterface  $key  The decryption key
     * @param  string  $associatedData  Optional associated data that was authenticated during encryption
     * @return string The decrypted plaintext
     *
     * @throws Exception If decryption fails (e.g., due to authentication failure)
     */
    public function decrypt(string $ciphertext, KeyInterface $key, string $associatedData = ''): string;
}
