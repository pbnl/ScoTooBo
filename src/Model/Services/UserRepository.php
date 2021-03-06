<?php

namespace App\Model\Services;

use App\Entity\LDAP\PbnlAccount;
use App\Entity\LDAP\PosixGroup;
use App\Model\Filter;
use App\Model\LdapComponent\PbnlLdapEntityManager;
use App\Model\SSHA;
use App\Model\User;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRepository implements UserProviderInterface
{

    /**
     * A reference to the LdapEntityService to work with the ldap
     *
     * @var PbnlLdapEntityManager
     */
    private $ldapEntityManager;

    /**
     * no direct ldap access. Pls use the group repo
     * @var
     */
    private $groupRepository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Validator
     */
    private $validator;

    private $pbnlAccountRepository;

    /**
     * The ldapManager of the LDAPBundle
     *
     * @param LoggerInterface $logger
     * @param PbnlLdapEntityManager $ldapEntityManager
     * @param ValidatorInterface $validator
     * @param GroupRepository $groupRepository
     */
    public function __construct(
        LoggerInterface $logger,
        PbnlLdapEntityManager $ldapEntityManager,
        ValidatorInterface $validator,
        GroupRepository $groupRepository
    )
    {
        $this->ldapEntityManager = $ldapEntityManager;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->groupRepository = $groupRepository;
        $this->lazyLoader = new UserLazyLoader($ldapEntityManager);
        $this->pbnlAccountRepository = $this->ldapEntityManager->getRepository(PbnlAccount::class);
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

        return $this->loadUserByUsername($user->getUid());
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
        return $this->getUserByUid($username);
    }

    /**
     * Searches the PbnlAccount (ldap) and returns a User
     * Find by GivenName
     *
     * @param string $uid
     * @return User
     */
    public function getUserByUid(String $uid)
    {
        $ldapPbnlAccount = $this->pbnlAccountRepository->findOneByUid($uid);

        if (count($ldapPbnlAccount) == 0) {
            throw new UserDoesNotExistException(
                sprintf('Uid "%s" does not exist.', $uid)
            );
        } elseif (count($ldapPbnlAccount) > 1) {
            throw new UserNotUniqueException("Der User mit der Uid " . $uid . " ist nicht einzigartig");
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
        $salt = SSHA::sshaGetSalt($ldapPbnlAccount->getUserPassword());

        $shaHashedPassword = SSHA::sshaGetHash($ldapPbnlAccount->getUserPassword());

        //Fill up the user
        $user = new User($ldapPbnlAccount->getGivenName(), $shaHashedPassword, $salt, $this->lazyLoader);
        $user->setDn($ldapPbnlAccount->getDn());
        $user->setCity($ldapPbnlAccount->getL());
        $user->setFirstName($ldapPbnlAccount->getCn());
        $user->setLastName($ldapPbnlAccount->getSn());
        $user->setUidNumber(intval($ldapPbnlAccount->getUidNumber()));
        $user->setMail($ldapPbnlAccount->getMail());
        $user->setGivenName($ldapPbnlAccount->getGivenName());
        $user->setUid($ldapPbnlAccount->getUid());
        $user->setPostalCode($ldapPbnlAccount->getPostalCode());
        $user->setMobilePhoneNumber($ldapPbnlAccount->getMobile());
        $user->setStreet($ldapPbnlAccount->getStreet());
        $user->setHomePhoneNumber($ldapPbnlAccount->getTelephoneNumber());
        $user->setStamm($ldapPbnlAccount->getOu());
        //TODO maybe use something else as the ou to determine the stamm of the user

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            throw new CorruptDataInDatabaseException(
                "The user " . $ldapPbnlAccount->getUid() . " is corrupt! " . (string)$errors
            );
        }

        return $user;
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

    /**
     * Returns all Users
     * You can filterByGroup or filterByName with the filter Object
     *
     * @param Filter $filter
     * @return array
     * @throws GroupNotFoundException If the group of the Filter does not exist
     */
    public function findAllUsersByComplexFilter(Filter $filter)
    {
        $users = array();

        //If there is a filter we can use
        /** @var $group PosixGroup */
        $group = [];
        if (isset($filter->getFilterAttributes()[0])) {
            if ($filter->getFilterAttributes()[0] == "filterByUid" && $filter->getFilterTexts()[0] != "") {
                $pbnlAccounts = $this->pbnlAccountRepository->findByComplex(
                    array("uid" => '*' . $filter->getFilterTexts()[0] . '*')
                );
            } elseif ($filter->getFilterAttributes()[0] == "filterByGroup" && $filter->getFilterTexts()[0] != "") {
                $group = $this->groupRepository->findByCn($filter->getFilterTexts()[0]);
                if ($group == []) {
                    throw new GroupNotFoundException("We cant find the group " . $filter->getFilterTexts()[0]);
                }
                $pbnlAccounts = $this->pbnlAccountRepository->findAll();
            } else {
                $pbnlAccounts = $this->pbnlAccountRepository->findAll();
            }
        } else {
            $pbnlAccounts = $this->pbnlAccountRepository->findAll();
        }

        /** @var $pbnlAccount PbnlAccount[] */
        foreach ($pbnlAccounts as $pbnlAccount) {
            $user = $this->entitiesToUser($pbnlAccount);

            if ($group != []) {
                if ($group->isDnMember($user->getDn())) {
                    array_push($users, $user);
                }
            } else {
                array_push($users, $user);
            }
        }

        return $users;
    }

    /**
     * Add a user to the database
     *
     * @param User $user
     * @return User
     */
    public function addUser(User $user)
    {
        $pbnlAccount = $this->userToEntities($user);

        if ($this->doesUserExist($user)) {
            throw new UserAlreadyExistException("The user " . $user->getUid() . " already exists.");
        }

        $pbnlAccount->setUidNumber($this->getNewUidNumber());
        $this->ldapEntityManager->persist($pbnlAccount);
        $this->ldapEntityManager->flush();

        return $user;
    }

    /**
     * Creates a PbnlAccount with the data of an User object
     *
     * @param $user
     * @return PbnlAccount
     */
    private function userToEntities(User $user)
    {
        //TODO: Stop generating an pbnlAccount get it from the repo if it exists
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setL($user->getCity());
        $pbnlAccount->setOu($user->getStamm());
        $pbnlAccount->setStreet($user->getStreet());
        $pbnlAccount->setPostalCode($user->getPostalCode());
        $pbnlAccount->setGivenName($user->getGivenName());
        $pbnlAccount->setUid($user->getUid());
        $pbnlAccount->setCn($user->getFirstName());
        $pbnlAccount->setSn($user->getLastName());
        $pbnlAccount->setMail($user->getMail());
        $pbnlAccount->setTelephoneNumber($user->getHomePhoneNumber());
        $pbnlAccount->setMobile($user->getMobilePhoneNumber());
        $pbnlAccount->setGidNumber("501");
        $pbnlAccount->setHomeDirectory("/home/" . $user->getUid());
        $pbnlAccount->setUidNumber($user->getUidNumber());
        if ($user->getClearPassword() != "") {
            $pbnlAccount->setUserPassword(SSHA::sshaPasswordGen($user->getClearPassword()));
        } else {
            $pbnlAccount->setUserPassword(SSHA::buildSsha($user->getPassword(), $user->getSalt()));
        }

        return $pbnlAccount;
    }

    /**
     * Checks if this user already exists
     * For this the function uses the uid and the uidNumber
     * @param $user
     * @return bool
     * @throws UserNotUniqueException if there are more than one user with the same uid or uidNumber
     */
    private function doesUserExist(User $user)
    {
        if ($this->doesUserUidExist($user->getUid()) || $this->doesUserUidNumberExist($user->getUidNumber())) {
            return true;
        }

        return false;
    }

    /**
     * Looks if a user with given uid exists
     *
     * @param $getUid
     * @return bool
     * @throws UserNotUniqueException if there are more than one user with the same uid
     */
    private function doesUserUidExist($getUid)
    {
        $users = $this->pbnlAccountRepository->findByUid($getUid);
        if (count($users) == 1) {
            return true;
        }
        if (count($users) > 1) {
            throw new UserNotUniqueException("The user with the uid " . $getUid . " is not unique!");
        }

        return false;
    }

    /**
     * Looks if a user with given uidNumber exists
     *
     * @param $getUidNumber
     * @return bool
     * @throws UserNotUniqueException if there are more than one user with the same uidNumber
     */
    private function doesUserUidNumberExist($getUidNumber)
    {
        $users = $this->pbnlAccountRepository->findByUidNumber($getUidNumber);
        if (count($users) == 1) {
            return true;
        }
        if (count($users) > 1) {
            throw new UserNotUniqueException("The user with the uid " . $getUidNumber . " is not unique!");
        }

        return false;
    }

    /**
     * Returns the next uidNumber
     * Its the highest uidNumber of the pbnlAccounts + 1
     * @return int
     */
    private function getNewUidNumber()
    {
        /** @var  $users User[] */
        $users = $this->pbnlAccountRepository->findAll();
        $highesUidNumber = 0;

        foreach ($users as $user) {
            if ($user->getUidNumber() > $highesUidNumber) {
                $highesUidNumber = $user->getUidNumber();
            }
        }

        return $highesUidNumber + 1;
    }

    /**
     * Updates the data of a user in the database if the user exist
     *
     * @param User $userToUpdate
     * @throws UserDoesNotExistException if you want to update a user that does not exist
     *
     */
    public function updateUser(User $userToUpdate)
    {
        if (!$this->doesUserExist($userToUpdate)) {
            throw new UserDoesNotExistException("The user " . $userToUpdate->getUid() . " does not exist.");
        }

        $pbnlAccountToUpdate = $this->userToEntities($userToUpdate);
        $this->ldapEntityManager->persist($pbnlAccountToUpdate);
    }

    /**
     * Deletes the user with the given uid
     *
     * @param $userToRemove
     */
    public function removeUser($userToRemove)
    {
        if ($this->doesUserExist($userToRemove)) {
            $pbnlAccountToURemove = $this->userToEntities($userToRemove);
            $this->ldapEntityManager->delete($pbnlAccountToURemove);
        }
    }

    /**
     * @param $dn
     * @return User
     * @throws UserDoesNotExistException if the user does not exist
     */
    public function findUserByDn($dn)
    {
        try {
            $ldapPbnlAccount = $this->ldapEntityManager->retrieveByDn($dn, PbnlAccount::class);
        } catch (ErrorException $e) {
            throw new UserDoesNotExistException("The user with the dn: " . $dn . " does not exist!");
        }
        if (count($ldapPbnlAccount) == 0) {
            throw new UserDoesNotExistException("The user with the dn: " . $dn . " does not exist!");
        }
        $user = $this->entitiesToUser($ldapPbnlAccount[0]);

        return $user;
    }

    /**
     * Searches for uid like the given one in the ldap
     *
     * @param string $uid
     * @return array
     */
    public function findUsersByUidLike(string $uid)
    {
        $ldapPbnlAccounts = $this->pbnlAccountRepository->findByUid("*$uid*");
        $users = array();
        foreach ($ldapPbnlAccounts as $account) {
            array_push($users, $this->entitiesToUser($account));
        }

        return $users;
    }

    private function throwsUserDoesNotExistExceptionIfArrayEmty($array)
    {
        if (count($array) == 0) {
            throw new UserDoesNotExistException();
        }
    }
}
