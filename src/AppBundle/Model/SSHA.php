<?php

namespace AppBundle\Model;

class SSHA
{
    const saltByteLength = 8;
    const shaByteLength = 20;

    public static function sshaPasswordVerify($hash, $password)
    {
        // skip the "{SSHA}"
        $b64 = substr($hash, strlen("{SSHA}"));

        // base64 decoded
        $b64_dec = base64_decode($b64);

        // the salt (given it is a 8byte one)
        $salt = substr($b64_dec, -SSHA::saltByteLength);

        // now compare
        $newSha = base64_encode(sha1($password . $salt, true) . $salt);

        if ($b64 == $newSha) {
            return true;
        } else {
            return false;
        }
    }

    public static function sshaPasswordGen($password)
    {
        $salt = openssl_random_pseudo_bytes(SSHA::saltByteLength, $cryptoStrong);
        return "{SSHA}".base64_encode(sha1($password . $salt, true) . $salt);
    }

    public static function sshaPasswordGenWithGivenSalt($password, $salt)
    {
        if(strlen($salt) != SSHA::saltByteLength) {
            throw new WrongSaltLengthException("Salt is not 8 byte long");
        }
        return "{SSHA}".base64_encode(sha1($password . $salt, true) . $salt);
    }

    public static function sshaGetSalt($ssha)
    {
        // skip the "{SSHA}"
        $b64 = substr($ssha, strlen("{SSHA}"));

        // base64 decoded
        $b64_dec = base64_decode($b64);

        // the salt (given it is a 8byte one)
        $salt = substr($b64_dec, -SSHA::saltByteLength);

        return $salt;
    }

    public static function sshaGetHash($ssha)
    {
        // skip the "{SSHA}"
        $b64 = substr($ssha, strlen("{SSHA}"));

        // base64 decoded
        $b64_dec = base64_decode($b64);

        $sha = substr($b64_dec, 0, SSHA::shaByteLength);

        return $sha;
    }
}
