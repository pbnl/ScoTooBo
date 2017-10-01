<?php

namespace AppBundle\Model\Services;

use AppBundle\Model\Entity\LDAP\PbnlAccount;
use AppBundle\Model\User;
use Monolog\Logger;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Ucsf\LdapOrmBundle\Ldap\LdapEntityManager;
use Ucsf\LdapOrmBundle\Repository\Repository;

class UserRepository implements UserProviderInterface
{

    /**
     * A reference to the LdapEntityService to work with the ldap
     *
     * @var LdapEntityManager
     */
    private $ldapEntityManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * The ldapManager of the LDAPBundle
     *
     * @param Logger $logger
     * @param LdapEntityManager $ldapEntityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(Logger $logger, LdapEntityManager $ldapEntityManager, ValidatorInterface $validator)
    {
        $this->ldapEntityManager = $ldapEntityManager;
        $this->logger = $logger;
        $this->validator = $validator;
    }

    /**
     * Searches the PbnlAccount (ldap) and returns a User
     * Find by GivenName
     *
     * @param string $givenName
     * @return User
     */
    public function getUserByGivenName(String $givenName)
    {
        /** @var Repository $pbnlAccountRepository */
        $pbnlAccountRepository = $this->ldapEntityManager->getRepository(PbnlAccount::class);

        /** @var PbnlAccount $ldapPbnlAccount[] */
        $ldapPbnlAccount = $pbnlAccountRepository->findOneByGivenName($givenName);

        if (count($ldapPbnlAccount) == 0) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $givenName)
            );
        }

        return $this->entitiesToUser($ldapPbnlAccount);
    }

    /**
     * Creates a User with the data of a PbnlAccount (ldap)
     * and later mayby a MySqlPbnlAccount
     *
     * @param PbnlAccount $ldapPbnlAccount
     * @return User
     * @throws CorruptDataInDatabaseException if the data in the database is corrupt
     */
    private function entitiesToUser(PbnlAccount $ldapPbnlAccount)
    {
        $b64 = substr($ldapPbnlAccount->getUserPassword(), strlen("{SSHA}"));

        // base64 decoded
        $b64_dec = base64_decode($b64);

        // the salt (given it is a 8byte one)
        $salt = substr($b64_dec, -8);
        // the sha1 part
        $hashedPassword = substr($b64_dec, 0, 20);

        //Fill up the user
        $user = new User($ldapPbnlAccount->getGivenName(), $hashedPassword, $salt, ["ROLE_USER"]);
        $user->setDn($ldapPbnlAccount->getDn());
        $user->setCity($ldapPbnlAccount->getL());
        $user->setFirstName($ldapPbnlAccount->getCn());
        $user->setSecondName($ldapPbnlAccount->getSn());
        $user->setUidNumber(intval($ldapPbnlAccount->getUidNumber()));
        $user->setMail($ldapPbnlAccount->getMail());
        $user->setUsername($ldapPbnlAccount->getGivenName());
        $user->setPostalCode($ldapPbnlAccount->getPostalCode());
        $user->setMobilePhoneNumber($ldapPbnlAccount->getMobile());
        $user->setStreet($ldapPbnlAccount->getStreet());
        $user->generatePasswordAndSalt($ldapPbnlAccount->getUserPassword());
        $user->setHomePhoneNumber($ldapPbnlAccount->getTelephoneNumber());

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $this->logger->addAlert((string) $errors);
            throw new CorruptDataInDatabaseException();
        }

        return $user;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        return $this->getUserByGivenName($username);
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the user is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
