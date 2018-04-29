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

    public function retrieve($entityName, LdapConnection $ldapConnection, $options = array())
    {
        if (isset($options['searchDn'])) {
            $searchDn = $options['searchDn'];
        } else {
            $searchDn = $this->baseDn;
        }

        $ldapFilterString = $this->optionsToLdapFilter($options, $entityName);

        $searchResult = $ldapConnection->ldap_search($searchDn, $ldapFilterString);
        $ldapEntries = $ldapConnection->ldap_get_entries($searchResult);

        return $this->ldapEntriesResultToObjects($ldapEntries);
    }

    public function retriveByDn($dn, LdapConnection $ldapConnection)
    {
        //TODO: Kann man dieses Filter benutzen : "" ?
        $searchResult = $ldapConnection->ldap_search($dn, "(objectClass=*)");
        $ldapEntries = $ldapConnection->ldap_get_entries($searchResult);

        return $this->ldapEntriesResultToObjects($ldapEntries);
    }

    private function ldapEntriesResultToObjects($ldapEntries)
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
        // TODO: Implement update() method.
    }

    public function delete($element, LdapConnection $ldapConnection)
    {
        //$ldapConnection->ldap_delete($element->getDn());
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

    public function persist(PbnlAccount $entity, LdapConnection $ldapConnection)
    {
        $entity->checkMust();

        if($this->doesEntityAlreadyExist($entity, $ldapConnection))
        {
            $this->update($entity, $ldapConnection);
        }
        else
        {
            $this->add($entity, $ldapConnection);
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

    private function doesEntityAlreadyExist(LdapEntity $entity, $ldapConnection, $checkOnly = TRUE)
    {
        $dn = $entity->getDn();
        $baseDN = $entity->getBaseDnFromDn();
        $uniqueIdentifier = $entity::$uniqueIdentifier;
        $uIdGetterName = "get".$uniqueIdentifier;

        $entities = $this->retrieve($this->getEntityName(get_class($entity)), $ldapConnection, [
            'searchDn' => $baseDN,
            'filter' => [ $uniqueIdentifier => $entity->$uIdGetterName() ]
        ]);

        if ($checkOnly) {
            return (count($entities) > 0);
        } else {
            return $entities;
        }

    }
}