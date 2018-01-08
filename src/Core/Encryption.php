<?php
/**
 * Part of the evias/nem-php package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under MIT License.
 *
 * This source file is subject to the MIT License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    evias/nem-php
 * @version    1.0.0
 * @author     Grégory Saive <greg@evias.be>
 * @license    MIT License
 * @copyright  (c) 2017, Grégory Saive <greg@evias.be>
 * @link       http://github.com/evias/nem-php
 */
namespace NEM\Core;

use NEM\Core\KeyPair;
use NEM\Core\Buffer;
use kornrunner\Keccak;

use RuntimeException;

class Encryption
{
    /**
     * PBKDF2 : Password Based Key Derivation Function
     *
     * For the name of selected hashing algorithms (i.e. md5,
     * sha256, haval160,4, etc..), see hash_algos() for a list
     * of supported algorithms.
     *
     * This method can be used when PBKDF2 must be used, typically with
     * NEM this is used to derive a Private key off a Password.
     * 
     * @param   string                  $algorithm  Which hash algorithm to use for key derivation.
     * @param   NEM\Core\Buffer         $password   Password for key derivation as *Buffer*.
     * @param   NEM\Core\Buffer         $salt       Salt for key derivation as *Buffer*.
     * @param   integer                 $count      Count of Derivation iterations.
     * @param   integer                 $keyLength  Length of produced Key (count of Bytes).
     * @return  NEM\Core\Buffer
     *
     * @throws  RuntimeException            On invalid hash algorithm (maybe missing php extension)
     * @throws  InvalidArgumentException    On negative *$keyLength* argument.
     * @throws  InvalidArgumentException    On invalid derivation iterations *$count* or invalid *$keyLength* arguments.
     */
    public static function derive($algorithm, Buffer $password, Buffer $salt, $count = 6000, $keyLength = 64) // 6000=NanoWallet, 64=512bits
    {
        if ($keyLength < 0) {
            throw new InvalidArgumentException('Cannot have a negative key-length for PBKDF2');
        }

        $algorithm  = strtolower($algorithm);

        if (!in_array($algorithm, hash_algos(), true)) {
            throw new RuntimeException('PBKDF2 ERROR: Invalid hash algorithm');
        }

        if ($count <= 0 || $keyLength <= 0) {
            throw new InvalidArgumentException('PBKDF2 ERROR: Invalid parameters.');
        }

        // Get binary data of derived key and wrap in Buffer
        return new Buffer(\hash_pbkdf2($algorithm, $password->getBinary(), $salt->getBinary(), $count, $keyLength, true), $keyLength);
    }

    /**
     * Helper to hash the provided buffer `data`'s content
     * with algorithm `algo`.
     * 
     * The hash algorithm can contain `keccak-256` for example.
     * 
     * @param   string              $algo
     * @param   \NEM\Core\Buffer    $data
     * @return  \NEM\Core\Buffer
     */
    public static function hash($algo, Buffer $data)
    {
        if (in_array($algo, hash_algos())) {
            $hash = hash($algo, $data->getBinary(), true);
        }
        if (strpos(strtolower($algo), "keccak") !== false) {
            $bits = (int) substr($algo, -3); // keccak-256, keccak-512, etc.

            // use Keccak instead of PHP hash()
            $hash = Keccak::hash($data->getBinary(), $bits, true);
        }
        else {
            throw new RuntimeException("Unsupported hash algorithm '" . $algo . "'.");
        }

        return new Buffer($hash);
    }

    /**
     * HMAC : Hash based Message Authentication Code
     *
     * A MAC authenticates a message. It is a signature based on a secret key (salt).
     *
     * @param   string              $algorithm  Which hash algorithm to use.
     * @param   NEM\Core\Buffer     $data
     * @param   NEM\Core\Buffer     $salt
     * @return  NEM\Core\Buffer
     */
    public static function hmac($algo, Buffer $data, Buffer $salt)
    {
        return new Buffer(hash_hmac($algo, $data->getBinary(), $salt->getBinary(), true));
    }

    /**
     * Generate a checksum of data buffer `data` and of length
     * `checksumLen`. Default length is 4 bytes.
     *
     * @param   string              $algo
     * @param   \NEM\Core\Buffer    $data
     * @param   integer             $checksumLen
     * @return  \NEM\Core\Buffer 
     */
    public static function checksum($algo, Buffer $data, $checksumLen = 4)
    {
        $hash = static::hash($algo, $data)->getBinary();
        $out = new Buffer(substr($hash, 0, $checksumLen), $checksumLen);
        return $out;
    }

    /**
     * Helper for encryption using a *sender private key* and *recipient public
     * key*.
     *
     * @param   string              $data               Plain text content of the Message to encrypt.
     * @param   \NEM\Core\KeyPair   $senderPrivateKey   Private Key of the Sender.
     * @param   \NEM\Core\KeyPair   $recipientPubKey    Public Key of the Recipient.
     * @return  string
     */
    public function encrypt($data, KeyPair $recipientPrivateKey, KeyPair $senderPublicKey)
    {
        return "";
    }

    /**
     * Helper for decryption using a *recipient private key* and *sender public
     * key*.
     *
     * @param   \NEM\Core\Buffer    $payload                An encrypted message payload.
     * @param   \NEM\Core\KeyPair   $recipientPrivateKey    Private Key of the Sender.
     * @param   \NEM\Core\KeyPair   $senderPubKey           Public Key of the Recipient.
     * @return  string
     */
    public function decrypt(Buffer $payload, KeyPair $recipientPrivateKey, KeyPair $senderPublicKey)
    {
        return "";
    }
}
