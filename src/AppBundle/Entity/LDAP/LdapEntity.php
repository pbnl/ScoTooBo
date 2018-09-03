<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 30.03.18
 * Time: 18:52
 */

namespace AppBundle\Entity\LDAP;


abstract class LdapEntity
{
    static $mustFields = [];
    static $uniqueIdentifier = "";

    /**
     * Absolute path to the user (in the LDAP)
     * @var string
     */
    protected $dn = "";

    protected function generateNewDn()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getDn()
    {
        if($this->dn == "") $this->setDn($this->generateNewDn());
        return $this->dn;
    }

    /**
     * @param string $dn
     */
    public function setDn($dn)
    {
        if($dn != "")
        {
            $ldapDnParts = ldap_explode_dn($dn , 1);
            if($ldapDnParts == FALSE) throw new \BadMethodCallException("DN you want to set is wrong");
        }
        $this->dn = $dn;
    }

    /**
     * @return string
     */
    public function getBaseDnFromDn() {
        if($this->dn == "") $this->setDn($this->generateNewDn());
        return substr($this->dn, stripos($this->dn, 'dc='));
    }

    public function checkMust()
    {
        foreach ($this::$mustFields as $mustField)
        {
            if($this->$mustField === "")
            {
                throw new MissingMustAttributeException();
            }
        }

        return true;
    }
}