<?php

namespace AppBundle\Model\LdapComponent\LdapEntryHandler;


use AppBundle\Entity\LDAP\LdapEntity;
use AppBundle\Entity\LDAP\PbnlAccount;
use AppBundle\Model\LdapComponent\EmptyMustFieldException;
use AppBundle\Model\LdapComponent\LdapConnection;
use AppBundle\Model\LdapComponent\Repositories\Repository;
use InvalidArgumentException;
use PHPUnit\Runner\Exception;

class PbnlAccountLdapHandler extends LdapEntryHandler
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
            throw new InvalidArgumentException("This class only supports the objectClass pbnlAccount");
        }
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setGivenName($ldapEntryArray["givenname"][0]);
        $pbnlAccount->setUid($ldapEntryArray["uid"][0]);
        $pbnlAccount->setCN($ldapEntryArray["cn"][0]);
        $pbnlAccount->setSn($ldapEntryArray["sn"][0]);
        $pbnlAccount->setUidNumber($ldapEntryArray["uidnumber"][0]);
        $pbnlAccount->setMail($ldapEntryArray["mail"][0]);
        $pbnlAccount->setUserPassword($ldapEntryArray["userpassword"][0]);
        $pbnlAccount->setHomeDirectory($ldapEntryArray["homedirectory"][0]);
        $pbnlAccount->setDn($ldapEntryArray["dn"]);
        $pbnlAccount->setMobile($ldapEntryArray["mobile"][0]);
        $pbnlAccount->setPostalCode($ldapEntryArray["postalcode"][0]);
        $pbnlAccount->setStreet($ldapEntryArray["street"][0]);
        $pbnlAccount->setTelephoneNumber($ldapEntryArray["telephonenumber"][0]);
        $pbnlAccount->setL($ldapEntryArray["l"][0]);
        $pbnlAccount->setGidNumber($ldapEntryArray["gidnumber"][0]);

        return $pbnlAccount;
    }

    public function update($element, LdapConnection $ldapConnection)
    {
        //TODO nicht alle attribute sollen/werden gestzt werden
        $data = array();
        $data["sn"] = $element->getSn();
        $data["uid"] = $element->getUid();
        $data["l"] = $element->getL();
        $data["mobile"] = $element->getMobile();
        $data["postalcode"] = $element->getPostalCode();
        $data["street"] = $element->getStreet();
        $data["telephonenumber"] = $element->getTelephoneNumber();
        $data["cn"] = $element->getCn();
        $data["homedirectory"] = $element->getHomeDirectory();
        $data["mail"] = $element->getMail();
        $data["userpassword"] = $element->getUserPassword();
        $data["gidnumber"] = $element->getGidNumber();

        //TODO: Do we realy want to use the @ operater?
        $succses = @$ldapConnection->ldap_modify($element->getDn(), $data);

        if (!$succses)
        {
            throw new LdapPersistException("Cant update Ldap element");
        }
    }

    public function add($element, LdapConnection $ldapConnection)
    {
        $userForLDAP = array();
        $userForLDAP["objectclass"][0] = "inetOrgPerson";
        $userForLDAP["objectclass"][1] = "posixAccount";
        $userForLDAP["objectclass"][2] = "pbnlAccount";
        $userForLDAP["cn"][0] = $element->getCn();
        $userForLDAP["gidnumber"][0] = "501";
        $userForLDAP["uidnumber"][0] = $element->getUidNumber();
        $userForLDAP["homedirectory"][0] = $element->getHomeDirectory();
        $userForLDAP["sn"][0] = $element->getSn();
        $userForLDAP["uid"][0] = $element->getUid();
        $userForLDAP["l"][0] = $element->getL();
        $userForLDAP["mail"][0] =  $element->getMail();
        $userForLDAP["mobile"][0] = $element->getMobile();
        $userForLDAP["postalcode"][0] = $element->getPostalCode();
        $userForLDAP["street"][0] = $element->getStreet();
        $userForLDAP["telephonenumber"][0] = $element->getTelephoneNumber();
        $userForLDAP["userpassword"][0] = $element->getUserPassword();
        $userForLDAP["givenname"][0] = $element->getGivenName();

        $dn = $element->getDn();

        //TODO: Do we realy want to use the @ operater?
        $succses = @$ldapConnection->ldap_add( $dn, $userForLDAP);

        if (!$succses)
        {
            throw new LdapPersistException("Cant add new Ldap element");
        }

    }

    private function isValidEntryArray($ldapEntryArray)
    {
        if($ldapEntryArray['objectclass'] == "pbnlAccount"
        || (is_array($ldapEntryArray['objectclass']) && in_array("pbnlAccount", $ldapEntryArray['objectclass']))
        )
            return true;
        else return false;
    }
}