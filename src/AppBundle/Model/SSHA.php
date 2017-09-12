<?php

namespace AppBundle\Model;

class SSHA
{
    static  public function ssha_password_verify($hash, $password){
        // skip the "{SSHA}"
        $b64 = substr($hash, 6);

        // base64 decoded
        $b64_dec = base64_decode($b64);

        // the salt (given it is a 8byte one)
        $salt = substr($b64_dec, -8);
        // the sha1 part
        $sha = substr($b64_dec, 0,20);

        // now compare
        $newSha = base64_encode( sha1($password . $salt,TRUE) . $salt );

        if ($b64 == $newSha) {
            return True;
        } else {
            return False;
        }
    }
    static public function ssha_password_gen($password)
    {
        $salt = openssl_random_pseudo_bytes(8, $cryptoStrong);
        return "{SSHA}".base64_encode( sha1($password . $salt,TRUE) . $salt );
    }
}