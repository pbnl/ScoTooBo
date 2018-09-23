<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 31.10.17
 * Time: 23:15
 */

namespace AppBundle\Model\LdapComponent;


use AppBundle\Entity\LDAP\PbnlAccount;
use AppBundle\Entity\LDAP\PbnlMailAlias;
use AppBundle\Entity\LDAP\PosixGroup;
use AppBundle\Model\LdapComponent\LdapEntryHandler\PbnlAccountLdapHandler;
use AppBundle\Model\LdapComponent\LdapEntryHandler\PosixGroupLdapHandler;
use AppBundle\Model\LdapComponent\Repositories\Repository;
use BadMethodCallException;
use Monolog\Logger;

/**
 * Class PbnlLdapEntityManager
 *
 * Interface to get the repositories to access the LDAP database an to perist / delete entities
 */
class PbnlLdapEntityManager
{
    private $uri = "";
    private $bindDN = "";
    private $password = "";
    private $useTLS = false;
    private $baseDN = "";

    private $ldapConnection;

    /**
     * LdapEntityManager constructor.
     *
     * @param Logger $logger
     *
     * @param $config
     */
    public function __construct(Logger $logger, $config)
    {
        $this->logger = $logger;
        $this->uri = $config['uri'];
        $this->bindDN = $config['bind_dn'];
        $this->password = $config['password'];
        $this->useTLS = $config['use_tls'];
        $this->baseDN = $config['base_dn'];

        $this->connect();
    }

    /**
     * Persist an instance in Ldap
     * @param unknown_type $entity
     */
    public function persist($entity)
    {
        if ($entity instanceof PbnlAccount)
        {
            $ldapHandler = new PbnlAccountLdapHandler($this->baseDN);
        }
        elseif ($entity instanceof PosixGroup)
        {
            $ldapHandler = new PosixGroupLdapHandler($this->baseDN);
        }
        //TODO: Add other classes
        else
        {
            throw new BadMethodCallException("Class ".get_class($entity)." is not supported");
        }
        $ldapHandler->persist($entity, $this->ldapConnection);
    }

    /**
     * Delete an instance in Ldap
     * @param $entity
     * @throws LdapEntryHandler\LdapPersistException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function delete($entity)
    {
        if ($entity instanceof PbnlAccount)
        {
            $ldapHandler = new PbnlAccountLdapHandler($this->baseDN);
        }
        //TODO: Add other classes
        else
        {
            throw new BadMethodCallException("Class ".get_class($entity)." is not supported");
        }
        $ldapHandler->delete($entity, $this->ldapConnection);
    }

    /**
     * Delete an entry in ldap by Dn
     * @param string $dn
     */
    public function deleteByDn($dn, $recursive=false)
    {

    }

    /**
     * Send entity to database
     */
    public function flush()
    {
        return;
    }

    /**
     * Gets the repository for an entity class.
     *
     * @param string $entityName The name of the entity.
     *
     * @return Repository The repository class.
     */
    public function getRepository($entityName)
    {
        $searchableAttributes = array();

        $ef = PbnlAccount::class;
        //TODO: maybe move searchable Attr into class
        if($entityName === PbnlAccount::class)
        {
            $searchableAttributes = ["cn", "sn", "givenName", "uid", "uidNumber"];
        }
        elseif($entityName === PbnlMailAlias::class)
        {
            $searchableAttributes = [""];
        }
        elseif($entityName === PosixGroup::class)
        {
            $searchableAttributes = ["cn", "gidNumber"];
        }
        else
        {
            throw new BadMethodCallException("Class $entityName is not supported");
        }

        return new Repository($this, $entityName, $searchableAttributes);
    }

    /**
     * Loads the objects of the type $entityName out of the ldap using the given options
     *
     * @param string $entityName
     * @param array $options
     * @return mixed
     */
    public function retrieve(string $entityName, array $options)
    {
        $handlerClass = $this->buildLdapHandlerClassName($entityName);

        $ldapEntryHandler = new $handlerClass($this->baseDN);

        $objects = $ldapEntryHandler->retrieve($entityName, $this->ldapConnection, $options);

        return $objects;
    }

    /**
     * gets an entity name an generates the class name (with path) for the corresponding ldap handler
     *
     * @param $entityName
     * @return string
     */
    private function buildLdapHandlerClassName($entityName)
    {
        return "AppBundle\Model\LdapComponent\LdapEntryHandler\\".$entityName . "LdapHandler";
    }

    /**
     * Connect to LDAP service
     *
     * @return LDAP resource
     */
    private function connect()
    {
        $this->ldapConnection = new LdapConnection($this->uri, $this->useTLS, $this->password, $this->bindDN);
        $this->ldapConnection->openConnection();
    }

    public function retrieveByDn($dn, $entityName)
    {
        $handlerClass = $this->buildLdapHandlerClassName($this->getEntityName($entityName));

        $ldapEntryHandler = new $handlerClass($this->baseDN);

        $objects = $ldapEntryHandler->retriveByDn($dn, $this->ldapConnection);

        return $objects;
    }

    private function getEntityName($class)
    {
        $entityName = explode("\\",$class);
        $entityNameWithoutPath = end($entityName);

        return $entityNameWithoutPath;
    }
}