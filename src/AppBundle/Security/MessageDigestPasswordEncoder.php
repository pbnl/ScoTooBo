<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 28.03.17
 * Time: 19:36
 */
namespace AppBundle\Security;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder as BaseMessageDigestPasswordEncoder;

class MessageDigestPasswordEncoder extends BaseMessageDigestPasswordEncoder
{
    private $algorithm;
    private $encodeHashAsBase64;

    public function __construct($algorithm = 'sha512', $encodeHashAsBase64 = true, $iterations = 5000)
    {
        $this->algorithm = $algorithm;
        $this->encodeHashAsBase64 = $encodeHashAsBase64;
    }
    protected function mergePasswordAndSalt($password, $salt)
    {
        if (empty($salt)) {
            return $password;
        }
        return $salt.$password;
    }
    public function encodePassword($raw, $salt)
    {
        $newSha = sha1($raw . $salt,TRUE);
        return $newSha;
    }
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }
}