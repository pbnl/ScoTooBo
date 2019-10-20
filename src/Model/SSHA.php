<?php

namespace App\Model;

class SSHA
{
    const SALTBYTELENGTH = 8;
    const SHABYTELENGTH = 20;

    public static function sshaPasswordVerify($hash, $password)
    {
        // skip the "{SSHA}"
        $b64 = substr($hash, strlen("{SSHA}"));

        // base64 decoded
        $b64_dec = base64_decode($b64);

        // the salt (given it is a 8byte one)
        $salt = substr($b64_dec, -SSHA::SALTBYTELENGTH);

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
        $salt = openssl_random_pseudo_bytes(SSHA::SALTBYTELENGTH, $cryptoStrong);
        return "{SSHA}" . base64_encode(sha1($password . $salt, true) . $salt);
    }

    public static function sshaPasswordGenWithGivenSalt($password, $salt)
    {
        if (strlen($salt) != SSHA::SALTBYTELENGTH) {
            throw new WrongSaltLengthException("Salt is not 8 byte long");
        }
        return "{SSHA}" . base64_encode(sha1($password . $salt, true) . $salt);
    }

    public static function sshaGetSalt($ssha)
    {
        // skip the "{SSHA}"
        $b64 = substr($ssha, strlen("{SSHA}"));

        // base64 decoded
        $b64_dec = base64_decode($b64);

        // the salt (given it is a 8byte one)
        $salt = substr($b64_dec, -SSHA::SALTBYTELENGTH);

        return $salt;
    }

    public static function sshaGetHash($ssha)
    {
        // skip the "{SSHA}"
        $b64 = substr($ssha, strlen("{SSHA}"));

        // base64 decoded
        $b64_dec = base64_decode($b64);

        $sha = substr($b64_dec, 0, SSHA::SHABYTELENGTH);

        return $sha;
    }

    public static function buildSsha($shaHashedPassword, $salt)
    {
        return "{SSHA}" . base64_encode($shaHashedPassword . $salt);
    }
}
