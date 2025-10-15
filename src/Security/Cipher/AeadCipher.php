<?php

declare(strict_types = 1);

namespace Upward\Paileys\Security\Cipher;

use Exception;
use Random\RandomException;
use SodiumException;
use Upward\Paileys\Contracts\Security\Cipher;
use Upward\Paileys\Contracts\Security\KeyInterface;

/**
 * Authenticated Encryption with Associated Data (AEAD) Cipher
 *
 * This implementation uses the XChaCha20-Poly1305 algorithm from libsodium,
 * which provides both confidentiality and integrity protection.
 */
class AeadCipher implements Cipher
{
    /**
     * {@inheritdoc}
     * @throws RandomException|SodiumException
     */
    public function encrypt(string $plaintext, KeyInterface $key, string $associatedData = ''): string
    {
        // Generate random nonce for this encryption
        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);

        // Encrypt the plaintext with the key, nonce, and associated data
        $ciphertext = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
            $plaintext,
            $associatedData,
            $nonce,
            $key->bytes,
        );

        // Prepend the nonce to the ciphertext
        return $nonce . $ciphertext;
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt(string $ciphertext, KeyInterface $key, string $associatedData = ''): string
    {
        // Extract the nonce from the beginning of the ciphertext
        $nonceLength = SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;

        if (strlen($ciphertext) < $nonceLength) {
            throw new Exception('Invalid ciphertext: too short');
        }

        $nonce = substr($ciphertext, 0, $nonceLength);
        $encryptedData = substr($ciphertext, $nonceLength);

        // Decrypt the ciphertext with the key, nonce, and associated data
        $plaintext = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
            $encryptedData,
            $associatedData,
            $nonce,
            $key->bytes,
        );

        if ($plaintext === false) {
            throw new Exception('Decryption failed: authentication failed');
        }

        return $plaintext;
    }
}
