<?php

namespace AppBundle\Model;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, EquatableInterface
{

    /**
     * Is also the name of the LDAP entry
     * Maps to the givenName and the uid
     *
     * @var string
     */
    private $username = "";

    /**
     * Real first name
     *
     * @var string
     */
    private $firstName = "";

    /**
     * Real second name
     * @var string
     */
    private $secondName = "";

    /**
     * The user number (should be unique)
     *
     * @var int
     */
    private $uidNumber = 0;

    /**
     * The internal "@pbnl" mail address
     *
     * @var string
     */
    private $mail = "";

    /**
     * SSHA hashed user password
     *
     * @var string
     */
    private $hashedPassword = "";

    /**
     * The salt of the password
     *
     * @var string
     */
    private $salt = "";


    /**
     * The absolute path to the user (in the LDAP)
     *
     * @var string
     */
    private $dn = "";

    /**
     * The mobile number
     *
     * @var string
     */
    private $mobilePhoneNumber = "0";

    /**
     * The postal code (PLZ)
     *
     * @var string
     */
    private $postalCode = "0";

    /**
     * The full address of the user (without postal code)
     *
     * @var string
     */
    private $street = "0";

    /**
     * The telephone number of the users home
     *
     * @var string
     */
    private $homePhoneNumber = "0";

    /**
     * The city the user lives in
     *
     * @var string
     */
    private $city = "0";

    /**
     * The roles of the user
     *
     * @var array
     */
    private $roles;

    /**
     * User constructor.
     * @param $username
     * @param $password
     * @param $salt
     * @param array $roles
     * @internal param string $dn
     */
    public function __construct($username, $password, $salt, array $roles)
    {
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getSecondName(): string
    {
        return $this->secondName;
    }

    /**
     * @param string $secondName
     */
    public function setSecondName(string $secondName)
    {
        $this->secondName = $secondName;
    }

    /**
     * @return int
     */
    public function getUidNumber(): int
    {
        return $this->uidNumber;
    }

    /**
     * @param int $uidNumber
     */
    public function setUidNumber(int $uidNumber)
    {
        $this->uidNumber = $uidNumber;
    }

    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     */
    public function setMail(string $mail)
    {
        $this->mail = $mail;
    }


    /**
     * @return string
     */
    public function getMobilePhoneNumber(): string
    {
        return $this->mobilePhoneNumber;
    }

    /**
     * @param string $mobilePhoneNumber
     */
    public function setMobilePhoneNumber(string $mobilePhoneNumber)
    {
        $this->mobilePhoneNumber = $mobilePhoneNumber;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getHomePhoneNumber(): string
    {
        return $this->homePhoneNumber;
    }

    /**
     * @param string $homePhoneNumber
     */
    public function setHomePhoneNumber(string $homePhoneNumber)
    {
        $this->homePhoneNumber = $homePhoneNumber;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getDn(): string
    {
        return $this->dn;
    }

    /**
     * @param string $dn
     */
    public function setDn(string $dn)
    {
        $this->dn = $dn;
    }



    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @param string $hashedPassword
     */
    public function setPassword(string $hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * Splits up the ldap SSHA hash into the salt and the hash and saves it in the fields
     *
     * @param $getUserPassword
     */
    public function generatePasswordAndSalt($getUserPassword)
    {
        // skip the "{SSHA}"
        $b64 = substr($getUserPassword, 6);

        // base64 decoded
        $b64_dec = base64_decode($b64);

        // the salt (given it is a 8byte one)
        $this->salt = substr($b64_dec, -8);
        // the sha1 part
        $this->hashedPassword = substr($b64_dec, 0, 20);
    }
}
