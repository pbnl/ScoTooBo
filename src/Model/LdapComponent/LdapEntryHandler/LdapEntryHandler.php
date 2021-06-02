<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 17.11.2017
 * Time: 15:48
 */

namespace App\Model\LdapComponent\LdapEntryHandler;


use App\Entity\LDAP\LdapEntity;
use App\Model\LdapComponent\LdapConnection;
use App\Model\LdapComponent\LdapFilter;
use Doctrine\ORM\EntityNotFoundException;

abstract class LdapEntryHandler
{
    protected $baseDn = "";

    public function __construct(string $baseDn)
    {
        $this->baseDn = $baseDn;
    }

    public function persist(LdapEntity $entity, LdapConnection $ldapConnection)
    {
        $entity->checkMust();

        if ($this->doesEntityAlreadyExist($entity, $ldapConnection)) {
            $this->update($entity, $ldapConnection);
        } else {
            $this->add($entity, $ldapConnection);
        }
    }

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
        $searchResult = $ldapConnection->ldap_search($dn, "(objectClass=*)");
        if ($searchResult != false) {
            $ldapEntries = $ldapConnection->ldap_get_entries($searchResult);
            return $this->ldapEntriesResultToObjects($ldapEntries);
        }
        else {
            return [];
        }
    }

    protected abstract function ldapEntriesResultToObjects($ldapEntries);

    public abstract function update($element, LdapConnection $ldapConnection);

    public function delete($entity, LdapConnection $ldapConnection)
    {
        if ($this->doesEntityAlreadyExist($entity, $ldapConnection)) {
            //TODO: Do we realy want to use the @ operater?
            $succses = @$ldapConnection->ldap_delete($entity->getDn());

            if (!$succses) {
                throw new LdapPersistException("Cant add new Ldap element");
            }
        } else {
            throw new EntityNotFoundException("Entity with the dn " . $entity->getDn() . " does not exist -> You cant delet it");
        }
    }

    public abstract function add($element, LdapConnection $ldapConnection);

    private function doesEntityAlreadyExist(LdapEntity $entity, $ldapConnection, $checkOnly = TRUE)
    {
        $baseDN = $entity->getBaseDnFromDn();
        $uniqueIdentifier = $entity::$uniqueIdentifier;
        $uIdGetterName = "get" . $uniqueIdentifier;

        $entities = $this->retrieve($this->getEntityName(get_class($entity)), $ldapConnection, [
            'searchDn' => $baseDN,
            'filter' => [$uniqueIdentifier => $entity->$uIdGetterName()]
        ]);

        if ($checkOnly) {
            return (count($entities) > 0);
        } else {
            return $entities;
        }

    }

    protected function optionsToLdapFilter($options, string $objectClass)
    {

        // Discern LDAP filter
        if (empty($options['filter'])) {
            $filter = '(objectClass=' . $objectClass . ')';
        } else {
            if (is_array($options['filter'])) {
                $options['filter'] = array(
                    '&' => array(
                        'objectClass' => $objectClass,
                        $options['filter']
                    )
                );
                $ldapFilter = new LdapFilter($options['filter']);
                $filter = $ldapFilter->format();
            } else if (is_a($options['filter'], LdapFilter::class)) {
                $options['filter']->setFilterArray(
                    array(
                        '&' => array(
                            'objectClass' => $objectClass,
                            $options['filter']->getFilterArray()
                        )
                    )
                );
                $filter = $options['filter']->format();
            } else { // assume pre-formatted scale/string filter value
                $filter = '(&(objectClass=' . $objectClass . ')' . $options['filter'] . ')';
            }
        }

        return $filter;
    }

    protected function getEntityName($class)
    {
        $entityName = explode("\\", $class);
        $entityNameWithoutPath = end($entityName);

        return $entityNameWithoutPath;
    }
}