<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 11.11.17
 * Time: 21:53
 */

namespace App\Tests\LdapComponent;


use App\Entity\LDAP\PbnlAccount;
use App\Entity\LDAP\PbnlMailAlias;
use App\Entity\LDAP\PosixGroup;
use App\Model\LdapComponent\PbnlLdapEntityManager;
use App\Model\LdapComponent\Repositories\Repository;
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