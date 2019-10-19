<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 31.10.17
 * Time: 23:16
 */

namespace App\Model\LdapComponent\Repositories;


use App\Model\LdapComponent\LdapFilter;
use App\Model\LdapComponent\PbnlLdapEntityManager;
use BadMethodCallException;
use Doctrine\Bundle\DoctrineBundle\Mapping\ClassMetadataCollection;
use ReflectionClass;

class Repository
{
    protected $em, $it;
    private $entityName;
    private $searchableAttributes;

    /**
     * Build the LDAP repository for the given entity type (i.e. class)
     *
     * @param PbnlLdapEntityManager $em
     */
    public function __construct(PbnlLdapEntityManager $em, string $class, array $searchableAttributes) {
        $this->em = $em;
        $this->entityName = $this->getEntityName($class);
        $this->searchableAttributes = $searchableAttributes;
    }

    /**
     * Adds support for magic finders.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return array|object The found entity/entities.
     * @throws BadMethodCallException  If the method called is an invalid find* method
     *                                 or no find* method at all and therefore an invalid
     *                                 method call.
     */
    public function __call($method, $arguments) {
        switch (true) {
            case (0 === strpos($method, 'findBy')):
                $by = lcfirst(substr($method, 6));
                $method = 'findBy';
                break;

            case (0 === strpos($method, 'findOneBy')):
                $by = lcfirst(substr($method, 9));
                $method = 'findOneBy';
                break;

            default:
                throw new \BadMethodCallException(
                    "Undefined method '$method'. The method name must start with " .
                    "either findBy or findOneBy!"
                );
        }

        if (!array_key_exists($by, $this->searchableAttributes) && !in_array($by, $this->searchableAttributes)) {
            throw new \BadMethodCallException("No sutch searchable ldap attribute $by in $this->entityName");
        }

        return $this->$method(
            $by, // attribute name
            $arguments[0], // attribute value
            empty($arguments[1]) ? null : $arguments[1] // attribute list
        );
    }

    /**
     * Simple LDAP search for all entries within the current repository
     * @return array An array of LdapEntity objects
     */
    public function findAll($attributes = null) {
        $options = array();
        if ($attributes != null) {
            $options['attributes'] = $attributes;
        }
        return $this->em->retrieve($this->entityName, $options);
    }

    /**
     * Simple LDAP search with a single attribute name/value pair
     * within the current repository
     * @param string $varname LDAP attribute name
     * @param string $value LDAP vattribute value
     * @return array An array of LdapEntity objects
     */
    public function findBy($varname, $value, $attributes = null) {
        $options = array();
        $options['filter'] = new LdapFilter(array($varname => $value));
        if ($attributes != null) {
            $options['attributes'] = $attributes;
        }
        return $this->em->retrieve($this->entityName, $options);
    }


    /**
     * Return an object or objects with corresponding varname as Criteria.
     * @todo This should return an error when more than one is found
     * @param string $varname LDAP attribute name
     * @param string $value LDAP vattribute value
     * @return array LdapEntity

     */
    public function findOneBy($varname, $value, $attributes = null) {
        $r = $this->findBy($varname, $value, $attributes);
        if (empty($r[0])) {
            return array();
        } else {
            return $r[0];
        }
    }


    /**
     * Create a complex LDAP filter string from an multi-dimensional array of LDAP filter
     * operators and operands.
     *
     * $mixed is always an associative array at the top level, with the key containing a LDAP
     * filter operator and the value containing another associative array that can:
     *
     * @param $filterArray
     * @param null $attributes
     * @return string LDAP filter string
     * @internal param array $mixed An associative array of LDAP filter operators and operands
     */
    public function findByComplex($filterArray, $attributes = null) {
        $options = array();
        $options['filter'] = new LdapFilter($filterArray);
        $options['attributes'] = $attributes;
        return $this->em->retrieve($this->entityName, $options);
    }

    private function getEntityName($class)
    {
        $entityName = explode("\\",$class);
        $entityNameWithoutPath = end($entityName);

        return $entityNameWithoutPath;
    }
}