<?php

namespace App\Model\Services;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserDoesNotExistException extends UsernameNotFoundException
{

}
