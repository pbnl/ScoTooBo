<?php

namespace AppBundle\Model\Entity\LDAP;

use Ucsf\LdapOrmBundle\Annotation\Ldap\Attribute;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Dn;
use Ucsf\LdapOrmBundle\Annotation\Ldap\ObjectClass;
use Ucsf\LdapOrmBundle\Annotation\Ldap\SearchDn;
use Ucsf\LdapOrmBundle\Annotation\Ldap\UniqueIdentifier;
use Ucsf\LdapOrmBundle\Entity\Ldap\InetOrgPerson;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a LDAPUser object class, which is a subclass of InetOrgPerson
 *
 * @ObjectClass("pbnlAccount")
 * @UniqueIdentifier("givenName")
 * @SearchDn("ou=people,dc=pbnl,dc=de")
 * @Dn("givenName={{ entity.givenName }},ou={{ entity.ou }},ou=people,dc=pbnl,dc=de")
 */
class PbnlAccount extends InetOrgPerson
{

    protected $ou;

    /**
     * Username
     * @var string
     * @Attribute("givenName")
     * @Assert\Regex("/^[\S]+$/") anything but space
     *
     * Is also the name of the LDAP entry
     */
    protected $givenName = "";

    /**
     * Username (should be the same as givenName but in lowercase and without ö,ä,ü)
     * @var string
     * @Attribute("uid")
     */
    protected $uid = "";

    /**
     * Real first name
     * @var string
     * @Attribute("cn")
     */
    protected $cn = "";

    /**
     * Real last name
     * @var string
     * @Attribute("sn")
     */
    protected $sn = "";

    /**
     * User number (should be unique)
     * @var int
     * @Attribute("uidNumber")
     * @Assert\Type("integer")
     */
    protected $uidNumber = "";

    /**
     * Internal "@pbnl" mail address
     * @var string
     * @Attribute("mail")
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    protected $mail = "";

    /**
     * SSHA hashed user password
     * @var string
     * @Attribute("userPassword")
     */
    protected $userPassword = "";

    /**
     * HomeDirecotry on server (no usage)
     * @var string
     * @Attribute("homeDirectory")
     */
    protected $homeDirectory = "";

    /**
     * Absolute path to the user (in the LDAP)
     * @var string
     */
    protected $dn = "";

    /**
     * Mobile number
     * @var string
     * @Attribute("mobile")
     */
    protected $mobile = "";

    /**
     *  the postal code (PLZ)
     * @var string
     * @Attribute("postalCode")
     * @Assert\Regex("/^[0-9]*$/") only numbers
     */
    protected $postalCode = "";

    /**
     * Full address of the user (without postal code)
     * @var string
     * @Attribute("street")
     */
    protected $street = "";

    /**
     * Telephone number of the users home
     * @var string
     * @Attribute("telephoneNumber")
     */
    protected $telephoneNumber = "";

    /**
     * City the user lives in
     * @var string
     * @Attribute("l")
     */
    protected $l = "";

    /**
     * Internal unix gidNumer (not used)
     * @var int
     * @Attribute("gidNumber")
     * @Assert\Type("integer")
     */
    protected $gidNumber = "";


    //All getters and setters

    /**
     * @return string
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param string $givenName
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getCn()
    {
        return $this->cn;
    }

    /**
     * @param string $cn
     */
    public function setCn($cn)
    {
        $this->cn = $cn;
    }

    /**
     * @return string
     */
    public function getSn()
    {
        return $this->sn;
    }

    /**
     * @param string $sn
     */
    public function setSn($sn)
    {
        $this->sn = $sn;
    }

    /**
     * @return int
     */
    public function getUidNumber()
    {
        return $this->uidNumber;
    }

    /**
     * @param int $uidNumber
     */
    public function setUidNumber($uidNumber)
    {
        $this->uidNumber = $uidNumber;
    }

    /**
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return string
     */
    public function getHomeDirectory()
    {
        return $this->homeDirectory;
    }

    /**
     * @param string $homeDirectory
     */
    public function setHomeDirectory($homeDirectory)
    {
        $this->homeDirectory = $homeDirectory;
    }

    /**
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * @param string $dn
     */
    public function setDn($dn)
    {
        $this->dn = $dn;

        $ldapDnParts = explode(",", $dn);
        $ouPart = $ldapDnParts[1];
        $ouName = explode("=", $ouPart)[1];

        $this->setOu($ouName);
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    /**
     * @param string $telephoneNumber
     */
    public function setTelephoneNumber($telephoneNumber)
    {
        $this->telephoneNumber = $telephoneNumber;
    }

    /**
     * @return string
     */
    public function getL()
    {
        return $this->l;
    }

    /**
     * @param string $l
     */
    public function setL($l)
    {
        $this->l = $l;
    }

    /**
     * @return int
     */
    public function getGidNumber()
    {
        return $this->gidNumber;
    }

    /**
     * @param int $gidNumber
     */
    public function setGidNumber($gidNumber)
    {
        $this->gidNumber = $gidNumber;
    }
}
