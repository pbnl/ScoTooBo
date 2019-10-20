<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 30.03.18
 * Time: 18:52
 */

namespace App\Entity\LDAP;


use Countable;

abstract class LdapEntity implements Countable
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
        if ($this->dn == "") $this->setDn($this->generateNewDn());
        return $this->dn;
    }

    /**
     * @param string $dn
     */
    public function setDn($dn)
    {
        if ($dn != "") {
            $ldapDnParts = ldap_explode_dn($dn, 1);
            if ($ldapDnParts == FALSE) throw new \BadMethodCallException("DN you want to set is wrong");
        }
        $this->dn = $dn;
    }

    /**
     * @return string
     */
    public function getBaseDnFromDn()
    {
        if ($this->dn == "") $this->setDn($this->generateNewDn());
        return substr($this->dn, stripos($this->dn, 'dc='));
    }

    public function checkMust()
    {
        foreach ($this::$mustFields as $mustField) {
            if ($this->$mustField === "") {
                throw new MissingMustAttributeException();
            }
        }

        return true;
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return 1;
    }
}