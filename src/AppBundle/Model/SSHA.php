<?php

namespace AppBundle\Model;

class SSHA
{
    public static function sshaPasswordVerify($hash, $password)
    {
        // skip the "{SSHA}"
        $b64 = substr($hash, 6);

        // base64 decoded
        $b64_dec = base64_decode($b64);

        // the salt (given it is a 8byte one)
        $salt = substr($b64_dec, -8);
        // the sha1 part
        $sha = substr($b64_dec, 0, 20);

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
        $salt = openssl_random_pseudo_bytes(8, $cryptoStrong);
        return "{SSHA}".base64_encode(sha1($password . $salt, true) . $salt);
    }
}
