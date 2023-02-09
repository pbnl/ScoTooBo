<?php

namespace App\Security;

use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;

class SSHAPasswordHasher implements LegacyPasswordHasherInterface
{
    use CheckPasswordLengthTrait;

    public function hash(string $plainPassword, string $salt = null): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException();
        }
        $newSha = sha1($plainPassword . $salt, true);
        return $newSha;
    }

    public function verify(string $hashedPassword, string $plainPassword, string $salt = null): bool
    {
        if ('' === $plainPassword || $this->isPasswordTooLong($plainPassword)) {
            return false;
        }
        return $hashedPassword === $this->hash($plainPassword, $salt);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }
}
