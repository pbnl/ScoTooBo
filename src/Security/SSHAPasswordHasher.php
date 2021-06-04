<?php

namespace App\Security;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class SSHAPasswordHasher implements PasswordHasherInterface
{

    public function hash(string $plainPassword, string $salt = null): string
    {
        $newSha = sha1($plainPassword . $salt, true);
        return $newSha;
    }

    public function verify(string $hashedPassword, string $plainPassword, string $salt = null): bool
    {
        return $hashedPassword === $this->hash($plainPassword, $salt);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }
}
