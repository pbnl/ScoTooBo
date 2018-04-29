<?php

namespace AppBundle\Entity\LDAP;

use Guzzle\Common\Exception\BadMethodCallException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a LDAPUser object class
 */
class PbnlAccount extends LdapEntity
{

    static $mustFields = ["cn","gidNumber","homeDirectory","sn","uid","uidNumber"];
    static $uniqueIdentifier = "uidNumber";

    protected $ou;

    /**
     * GivenName
     * @var string
     *
     * Is also the name of the LDAP entry
     */
    protected $givenName = "";

    /**
     * uid (should be the same as givenName but in lowercase and without ö,ä,ü and with _ for ' ')
     * Must be unique !!!! Add a number at the end if there is someone with the same uid
     * @var string
     * @Assert\Regex("/^[0-9,a-x,_]*$/")
     */
    protected $uid = "";

    /**
     * Real first name
     * @var string
     */
    protected $cn = "";

    /**
     * Real last name
     * @var string
     */
    protected $sn = "";

    /**
     * User number (must be unique)
     * @var string
     */
    protected $uidNumber = "0";

    /**
     * Internal "@pbnl" mail address
     * @var string
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    protected $mail = "";

    /**
     * SSHA hashed user password
     * @var string
     */
    protected $userPassword = "";

    /**
     * HomeDirecotry on server (no usage)
     * @var string
     * @Assert\Regex("/^[0-9,a-x,A-X,_,\/]*$/")
     */
    protected $homeDirectory = "";

    /**
     * @return string
     */
    public function getUserPassword(): string
    {
        return $this->userPassword;
    }

    /**
     * @param string $userPassword
     */
    public function setUserPassword(string $userPassword)
    {
        $this->userPassword = $userPassword;
    }

    /**
     * Mobile number
     * @var string
     */
    protected $mobile = "";

    /**
     *  the postal code (PLZ)
     * @var string
     * @Assert\Regex("/^[0-9]*$/") only numbers
     */
    protected $postalCode = "";

    /**
     * Full address of the user (without postal code and city)
     * @var string
     */
    protected $street = "";

    /**
     * Telephone number of the users home
     * @var string
     */
    protected $telephoneNumber = "";

    /**
     * City the user lives in
     * @var string
     */
    protected $l = "";

    /**
     * Internal unix gidNumer (not used)
     * default value is "501"
     * @var int
     */
    protected $gidNumber = "501";

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
     * @param mixed $ou
     */
    public function setOu($ou)
    {
        $this->ou = $ou;
    }

    /**
     * @return string
     */
    public function getUidNumber()
    {
        return $this->uidNumber;
    }

    /**
     * @param string $uidNumber
     */
    public function setUidNumber(string $uidNumber)
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
     * @param string $dn
     */
    public function setDn($dn)
    {
        $this->dn = $dn;

        if($dn != "")
        {
            $ldapDnParts = ldap_explode_dn($dn , 1);
            if($ldapDnParts == FALSE) throw new \BadMethodCallException("DN you want to set is wrong");
            $ouName = $ldapDnParts[1];
            $this->setOu($ouName);

        }
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
     * @return string
     */
    public function getGidNumber()
    {
        return $this->gidNumber;
    }

    /**
     * @return mixed
     */
    public function getOu()
    {
        return $this->ou;
    }

    /**
     * @param string $gidNumber
     */
    public function setGidNumber($gidNumber)
    {
        $this->gidNumber = $gidNumber;
    }

    /**
     * Generates a Dn based on the OU and the givenName
     */
    protected function generateNewDn()
    {
        if($this->getGivenName()== "" || $this->ou == "")
        {
            throw new BadMethodCallException("Cant generate DN: GivenName or Ou is empty ('')");
        }
        return "givenName=".$this->getGivenName().",ou=$this->ou,ou=People,dc=pbnl,dc=de";
    }

}
