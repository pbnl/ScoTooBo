<?php

namespace AppBundle\Model\LdapComponent\LdapEntryHandler;


use AppBundle\Entity\LDAP\LdapEntity;
use AppBundle\Entity\LDAP\PbnlAccount;
use AppBundle\Entity\LDAP\PosixGroup;
use AppBundle\Model\LdapComponent\EmptyMustFieldException;
use AppBundle\Model\LdapComponent\LdapConnection;
use AppBundle\Model\LdapComponent\Repositories\Repository;
use InvalidArgumentException;
use PHPUnit\Runner\Exception;

class PosixGroupLdapHandler extends LdapEntryHandler
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
            throw new InvalidArgumentException("This class only supports the objectClass posixGroup");
        }
        $posixGroup = new PosixGroup();
        $posixGroup->setCN($ldapEntryArray["cn"][0]);
        $posixGroup->setDn($ldapEntryArray["dn"]);
        $posixGroup->setGidNumber($ldapEntryArray["gidnumber"][0]);
        isset($ldapEntryArray["description"][0]) ? $posixGroup->setDescription($ldapEntryArray["description"][0]) : $posixGroup->setDescription("");
        $members = array();
        for ($i = 0; $i < $ldapEntryArray["memberuid"]["count"] ; $i++) {
            array_push($members, $ldapEntryArray["memberuid"][$i]);
        }
        $posixGroup->setMemberUid($members);

        return $posixGroup;
    }

    public function update($element, LdapConnection $ldapConnection)
    {
        //TODO nicht alle attribute sollen/werden gestzt werden
        $data = array();
        $data["objectClass"][0] = "posixGroup";
        $data["cn"][0] = $element->getCn();
        $data["gidnumber"][0] = $element->getGidNumber();
        empty($element->getDescription()) ? null : $data["description"][0] = $element->getDescription();
        foreach ($element->getMemberUid() as $key=>$memberUid) {
            if(!isset($data["memberUid"][$key])) {
                $data["memberUid"][$key] = $memberUid;
            }
        }

        //TODO: Do we realy want to use the @ operater?
        $succses = @$ldapConnection->ldap_modify($element->getDn(), $data);

        if (!$succses)
        {
            throw new LdapPersistException("Cant update Ldap element: ". $ldapConnection->getError());
        }
    }

    public function add($element, LdapConnection $ldapConnection)
    {
        $userForLDAP = array();
        $userForLDAP["objectclass"][0] = "posixGroup";
        $userForLDAP["cn"][0] = $element->getCn();
        $userForLDAP["gidnumber"][0] = $element->getGidNumber();
        $userForLDAP["description"][0] = $element->getDescription();
        foreach ($element->getMemberUid() as $key=>$memberUid) {
            $userForLDAP["memberUid"][$key] = $memberUid;
        }

        $dn = $element->getDn();

        //TODO: Do we realy want to use the @ operater?
        $succses = @$ldapConnection->ldap_add( $dn, $userForLDAP);

        if (!$succses)
        {
            throw new LdapPersistException("Cant add new Ldap element". ldap_error($ldapConnection));
        }

    }

    private function isValidEntryArray($ldapEntryArray)
    {
        if($ldapEntryArray['objectclass'] == "posixGroup"
        || (is_array($ldapEntryArray['objectclass']) && in_array("posixGroup", $ldapEntryArray['objectclass']))
        )
            return true;
        else return false;
    }
}