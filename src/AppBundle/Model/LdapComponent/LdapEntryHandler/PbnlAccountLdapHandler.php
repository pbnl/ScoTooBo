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
        $pbnlAccount->setUid($ldapEntryArray["uid"][0]);
        $pbnlAccount->setCN($ldapEntryArray["cn"][0]);
        $pbnlAccount->setSn($ldapEntryArray["sn"][0]);
        $pbnlAccount->setUidNumber($ldapEntryArray["uidnumber"][0]);
        isset($ldapEntryArray["givenname"][0]) ? $pbnlAccount->setGivenName($ldapEntryArray["givenname"][0]) : $pbnlAccount->setGivenName("=");
        isset($ldapEntryArray["mail"][0]) ? $pbnlAccount->setMail($ldapEntryArray["mail"][0]) : $pbnlAccount->setMail("");
        isset($ldapEntryArray["userpassword"][0]) ? $pbnlAccount->setUserPassword($ldapEntryArray["userpassword"][0]) : $pbnlAccount->setUserPassword("");
        $pbnlAccount->setHomeDirectory($ldapEntryArray["homedirectory"][0]);
        $pbnlAccount->setDn($ldapEntryArray["dn"]);
        isset($ldapEntryArray["mobile"][0]) ? $pbnlAccount->setMobile($ldapEntryArray["mobile"][0]) : $pbnlAccount->setMobile("");
        isset($ldapEntryArray["postalcode"][0]) ? $pbnlAccount->setPostalCode($ldapEntryArray["postalcode"][0]) : $pbnlAccount->setPostalCode("");
        isset($ldapEntryArray["street"][0]) ? $pbnlAccount->setStreet($ldapEntryArray["street"][0]) : $pbnlAccount->setStreet("");
        isset($ldapEntryArray["telephonenumber"][0]) ? $pbnlAccount->setTelephoneNumber($ldapEntryArray["telephonenumber"][0]) : $pbnlAccount->setTelephoneNumber("");
        isset($ldapEntryArray["l"][0]) ? $pbnlAccount->setL($ldapEntryArray["l"][0]) : $pbnlAccount->setL("");
        $pbnlAccount->setGidNumber($ldapEntryArray["gidnumber"][0]);

        return $pbnlAccount;
    }

    public function update($element, LdapConnection $ldapConnection)
    {
        //TODO nicht alle attribute sollen/werden gestzt werden
        $data = array();
        $data["sn"] = $element->getSn();
        $data["uidnumber"] = $element->getUidNumber();
        $data["uid"] = $element->getUid();
        !empty($element->getL()) ? $data["l"] = $element->getL() : "";
        !empty($element->getMobile()) ? $data["mobile"] = $element->getMobile() : "";
        !empty($element->getPostalCode()) ? $data["postalcode"] = $element->getPostalCode() : "";
        !empty($element->getStreet()) ? $data["street"] = $element->getStreet() : "";
        !empty($element->getTelephoneNumber()) ? $data["telephonenumber"] = $element->getTelephoneNumber() : "";
        $data["cn"] = $element->getCn();
        $data["homedirectory"] = $element->getHomeDirectory();
        !empty($element->getMail()) ? $data["mail"] = $element->getMail() : "";
        !empty($element->getUserPassword()) ? $data["userpassword"] = $element->getUserPassword() : "";
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
        $userForLDAP["uidnumber"] = $element->getUidNumber();
        $userForLDAP["sn"] = $element->getSn();
        $userForLDAP["uid"] = $element->getUid();
        !empty($element->getL()) ? $userForLDAP["l"] = $element->getL() : "";
        !empty($element->getMobile()) ? $userForLDAP["mobile"] = $element->getMobile() : "";
        !empty($element->getGivenName()) ? $userForLDAP["givenName"] = $element->getGivenName() : "";
        !empty($element->getPostalCode()) ? $userForLDAP["postalcode"] = $element->getPostalCode() : "";
        !empty($element->getStreet()) ? $userForLDAP["street"] = $element->getStreet() : "";
        !empty($element->getTelephoneNumber()) ? $userForLDAP["telephonenumber"] = $element->getTelephoneNumber() : "";
        $userForLDAP["cn"] = $element->getCn();
        $userForLDAP["homedirectory"] = $element->getHomeDirectory();
        !empty($element->getMail()) ? $userForLDAP["mail"] = $element->getMail() : "";
        !empty($element->getUserPassword()) ? $userForLDAP["userpassword"] = $element->getUserPassword() : "";
        $userForLDAP["gidnumber"] = $element->getGidNumber();

        $dn = $element->getDn();

        //TODO: Do we realy want to use the @ operater?
        $succses = @$ldapConnection->ldap_add( $dn, $userForLDAP);

        if (!$succses)
        {
            $error = $ldapConnection->getError();
            throw new LdapPersistException("Cant add new Ldap element $error");
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
