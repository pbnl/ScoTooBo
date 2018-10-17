<?php

namespace AppBundle\Model\LdapComponent\LdapEntryHandler;



use AppBundle\Entity\LDAP\PbnlMailAlias;
use AppBundle\Model\LdapComponent\LdapConnection;
use InvalidArgumentException;

class PbnlMailAliasLdapHandler extends LdapEntryHandler
{

    protected function ldapEntriesResultToObjects($ldapEntries)
    {
        $objects = array();

        for($i = 0; $i < $ldapEntries["count"]; $i++)
        {
            $oneObject = $this->ldapArrayToObject($ldapEntries[$i]);
            array_push($objects, $oneObject);
        }

        return $objects;
    }

    private function ldapArrayToObject($ldapEntryArray)
    {
        if(!$this->isValidEntryArray($ldapEntryArray))
        {
            throw new InvalidArgumentException("This class only supports the objectClass pbnlMailAlias");
        }
        $pbnlMailAlias = new PbnlMailAlias();
        $pbnlMailAlias->setMail($ldapEntryArray["mail"][0]);
        $pbnlMailAlias->setDn($ldapEntryArray["dn"]);
        $forward = array();
        for ($i = 0; $i < $ldapEntryArray["forward"]["count"] ; $i++) {
            array_push($forward, $ldapEntryArray["forward"][$i]);
        }
        $pbnlMailAlias->setForward($forward);

        return $pbnlMailAlias;
    }

    public function update($element, LdapConnection $ldapConnection)
    {
        //TODO nicht alle attribute sollen/werden gestzt werden
        $data = array();
        foreach ($element->getForward() as $key=>$forward) {
            $data["forward"][$key] = $forward;
        }

        //TODO: Do we realy want to use the @ operater?
        $succses = @$ldapConnection->ldap_modify($element->getDn(), $data);

        if (!$succses)
        {
            throw new LdapPersistException("Cant update Ldap element");
        }
    }

    public function add($element, LdapConnection $ldapConnection)
    {
        $forwardForLDAP = array();
        $forwardForLDAP["objectclass"][0] = "pbnlMailAlias";
        $forwardForLDAP["mail"][0] = $element->getMail();
        foreach ($element->getForward() as $key=>$forward) {
            $forwardForLDAP["forward"][$key] = $forward;
        }

        $dn = $element->getDn();

        //TODO: Do we realy want to use the @ operater?
        $succses = @$ldapConnection->ldap_add( $dn, $forwardForLDAP);

        if (!$succses)
        {
            throw new LdapPersistException("Cant add new Ldap element");
        }

    }

    private function isValidEntryArray($ldapEntryArray)
    {
        if($ldapEntryArray['objectclass'] == "pbnlMailAlias"
        || (is_array($ldapEntryArray['objectclass']) && in_array("pbnlMailAlias", $ldapEntryArray['objectclass']))
        )
            return true;
        else return false;
    }
}