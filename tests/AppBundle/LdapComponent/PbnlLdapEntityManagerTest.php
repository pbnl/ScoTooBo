<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 11.11.17
 * Time: 21:53
 */

namespace Tests\AppBundle\LdapComponent;


use AppBundle\Entity\LDAP\PbnlAccount;
use AppBundle\Entity\LDAP\PbnlMailAlias;
use AppBundle\Entity\LDAP\PosixGroup;
use AppBundle\Model\LdapComponent\PbnlLdapEntityManager;
use AppBundle\Model\LdapComponent\Repositories\Repository;
use BadMethodCallException;
use PHPUnit\Framework\TestCase;

class PbnlLdapEntityManagerTest extends TestCase
{

    public function testGetRepository()
    {
        $manager = $this->getMockBuilder(PbnlLdapEntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $repo = $manager->getRepository(PbnlAccount::class);
        $this->assertEquals(Repository::class, get_class($repo));
        $repo = $manager->getRepository(PosixGroup::class);
        $this->assertEquals(Repository::class, get_class($repo));
        $repo = $manager->getRepository(PbnlMailAlias::class);
        $this->assertEquals(Repository::class, get_class($repo));
    }

    public function testGetRepositoryBadArgument()
    {
        $this->expectException(BadMethodCallException::class);

        $manager = $this->getMockBuilder(PbnlLdapEntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $manager->getRepository("AClass");
    }
}