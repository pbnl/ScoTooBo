<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 17.11.2017
 * Time: 15:48
 */

namespace AppBundle\Model\LdapComponent\LdapEntryHandler;


use AppBundle\Entity\LDAP\LdapEntity;
use AppBundle\Model\LdapComponent\LdapConnection;
use AppBundle\Model\LdapComponent\LdapFilter;

abstract class LdapEntryHandler
{
    protected $baseDn = "";

    public function __construct(string $baseDn)
    {
        $this->baseDn = $baseDn;
    }

    public abstract function retrieve($entityName, LdapConnection $ldapConnection);

    public abstract function update($element, LdapConnection $ldapConnection);

    public abstract function delete($element, LdapConnection $ldapConnection);

    public abstract function add($element, LdapConnection $ldapConnection);

    protected function optionsToLdapFilter($options, string $objectClass)
    {

        // Discern LDAP filter
        if (empty($options['filter'])) {
            $filter = '(objectClass='.$objectClass.')';
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
            } else if (is_a ($options['filter'], LdapFilter::class)){
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
                $filter = '(&(objectClass='.$objectClass.')'.$options['filter'].')';
            }
        }

        return $filter;
    }

    protected function getEntityName($class)
    {
        $entityName = explode("\\",$class);
        $entityNameWithoutPath = end($entityName);

        return $entityNameWithoutPath;
    }
}