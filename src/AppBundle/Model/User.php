<?php

namespace AppBundle\Model;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

class User implements UserInterface, EquatableInterface
{

    /**
     * Name of the LDAP entry
     *
     * @var string
     */
    private $givenName = "";

    /**
     * Unique User id (same as givenName but without ' ' ä ö ü ß)
     *
     * @var string
     * @Assert\Regex("/^[0-9,a-z,_,.]*$/")
     */
    private $uid = "";

    /**
     * Real first name
     *
     * @var string
     */
    private $firstName = "";

    /**
     * Real last name
     *
     * @var string
     */
    private $lastName = "";

    /**
     * The user number (should be unique)
     *
     * @var int
     * @Assert\Type("integer")
     */
    private $uidNumber = 0;

    /**
     * The internal "@pbnl" mail address
     *
     * @var string
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = false
     * )
     */
    //TODO set checkMX from false to true, set it to false because of internet problems at home
    private $mail = "";

    /**
     * SHA hashed user password
     *
     * @var string
     */
    private $shaHashedPassword = "";

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
    private $mobilePhoneNumber = "";

    /**
     * The postal code (PLZ)
     *
     * @var string
     */
    private $postalCode = "";

    /**
     * The full address of the user (without postal code)
     *
     * @var string
     */
    private $street = "";

    /**
     * The telephone number of the users home
     *
     * @var string
     */
    private $homePhoneNumber = "";

    /**
     * The city the user lives in
     *
     * @var string
     */
    private $city = "";

    /**
     * The roles of the user
     *
     * @var array
     */
    private $roles;

    /**
     * Attribute needed for the password generation at the addUser page
     *
     * @var string
     */
    private $clearPassword = "";

    /**
     * Attribute needed for the password generation at the addUser page
     *
     * @var string
     */
    private $generatedPassword = "";

    /**
     * Stamm of the user
     * Will be the same as the ou the user is in
     * @var
     */
    private $stamm;

    /**
     * User constructor.
     * @param $uid
     * @param $password
     * @param $salt
     * @param array $roles
     * @internal param string $dn
     */
    public function __construct($uid, $shaHashedPassword, $salt, array $roles)
    {
        $this->uid = $uid;
        $this->shaHashedPassword = $shaHashedPassword;
        $this->salt = $salt;
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param mixed $givenName
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
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
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
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
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getClearPassword()
    {
        return $this->clearPassword;
    }

    /**
     * @param string $clearPassword
     */
    public function setClearPassword($clearPassword)
    {
        $this->clearPassword = $clearPassword;
    }

    /**
     * @return string
     */
    public function getGeneratedPassword()
    {
        return $this->generatedPassword;
    }

    /**
     * @param string $generatedPassword
     */
    public function setGeneratedPassword($generatedPassword)
    {
        $this->generatedPassword = $generatedPassword;
    }

    /**
     * @return mixed
     */
    public function getStamm()
    {
        return $this->stamm;
    }

    /**
     * @param mixed $stamm
     */
    public function setStamm($stamm)
    {
        $this->stamm = $stamm;
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
     * @param string $hashedPassword
     */
    public function setPassword(string $hashedPassword)
    {
        $this->shaHashedPassword = $hashedPassword;
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

        if ($this->getUid() !== $user->getUid()) {
            return false;
        }

        return true;
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
        return $this->shaHashedPassword;
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
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $unwanted_array = array(
            'Š' => 'S',
            'š' => 's',
            'Ž' => 'Z',
            'ž' => 'z',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'Ae',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'Oe',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'Ue',
            'Ý' => 'Y',
            'Þ' => 'B',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'ae',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'oe',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'ue',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'Ğ' => 'G',
            'İ' => 'I',
            'Ş' => 'S',
            'ğ' => 'g',
            'ı' => 'i',
            'ă' => 'a',
            'Ă' => 'A',
            'ș' => 's',
            'Ș' => 'S',
            'ț' => 't',
            'Ț' => 'T',
            ' ' => '_',
        );
        $uid = strtr($uid, $unwanted_array);

        $uid = strtolower($uid);

        $this->uid = $uid;
    }

    /**
     * Splits up the ldap SSHA hash into the salt and the hash and saves it in the fields
     *
     * @param $ssha
     */
    public function generatePasswordAndSalt($ssha)
    {
        $this->salt = SSHA::sshaGetSalt($ssha);
        $this->shaHashedPassword = SSHA::sshaGetHash($ssha);
    }
}
