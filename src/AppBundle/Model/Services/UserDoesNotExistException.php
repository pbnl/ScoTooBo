<?php

namespace AppBundle\Model\Services;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserDoesNotExistException extends UsernameNotFoundException
{

}
