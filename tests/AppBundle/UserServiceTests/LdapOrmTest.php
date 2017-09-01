<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 31.08.17
 * Time: 09:12
 */

namespace Tests\AppBundle\UserServicTest;

use AppBundle\Model\Entity\LDAP\PbnlAccount;
use AppBundle\Model\Entity\LDAP\PbnlMailAlias;
use AppBundle\Model\Entity\LDAP\PosixGroup;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LdapOrmTest extends WebTestCase
{
    public function testReadPbnlAccountORM()
    {

        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $ldapEntityManager = static::$kernel->getContainer()->get("ldapEntityManager");
        $personRepository = $ldapEntityManager->getRepository(PbnlAccount::class);
        $allPeople = $personRepository->findAll();
        $testAmbrone = $personRepository->findByGivenName("TestAmbrone1");

        $this->assertEquals(4, count($allPeople));

        $this->assertContains('/home/TestAmbrone1', $testAmbrone[0]->getHomeDirectory());
    }

    public function testcreateAndModDeletPbnlAccountORM()
    {

        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $ldapEntityManager = static::$kernel->getContainer()->get("ldapEntityManager");
        $personRepository = $ldapEntityManager->getRepository(PbnlAccount::class);

        $newOne = new PbnlAccount();
        $newOne->setObjectClass(["inetOrgPerson","posixAccount","pbnlAccount"]);
        $newOne->setGivenName("TestAccountToDelete");
        $newOne->setCn("TestAccountToDelete");
        $newOne->setHomeDirectory("/home/random");
        $newOne->setUid("TestAccountToDelete");
        $newOne->setUidNumber("1000000");
        $newOne->setSn("wedfef");
        $newOne->setGidNumber("501");
        $newOne->setOu("ambronen");

        $ldapEntityManager->persist($newOne);
        $ldapEntityManager->flush();

        $newOne = $personRepository->findByGivenName("TestAccountToDelete");
        $this->assertContains('/home/random', $newOne[0]->getHomeDirectory());

        $newOne[0]->setHomeDirectory("/home/random2");
        $newOne[0]->setSn("NiceName");
        $ldapEntityManager->persist($newOne[0]);

        $newOne = $personRepository->findByGivenName("TestAccountToDelete");
        $this->assertContains('/home/random2', $newOne[0]->getHomeDirectory());
        $this->assertContains('NiceName', $newOne[0]->getSn());

        $newOne = $personRepository->findByGivenName("TestAccountToDelete");
        $ldapEntityManager->delete($newOne[0]);
        $ldapEntityManager->flush();
        $newOne = $personRepository->findByGivenName("TestAccountToDelet");
        $this->assertEquals(0, count($newOne));
    }

    public function testPosixGroupORM()
    {

        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $ldapEntityManager = static::$kernel->getContainer()->get("ldapEntityManager");
        $personRepository = $ldapEntityManager->getRepository(PosixGroup::class);
        $allGroups = $personRepository->findAll();
        $ambronen = $personRepository->findByCn("ambronen");

        $this->assertEquals(10, count($allGroups));

        $this->assertContains('stammGroup', $ambronen[0]->getDescription());
    }

    public function testpbnlMailAliasORM()
    {

        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $ldapEntityManager = static::$kernel->getContainer()->get("ldapEntityManager");
        $personRepository = $ldapEntityManager->getRepository(PbnlMailAlias::class);
        $allForwards = $personRepository->findAll();
        $wiki = $personRepository->findByMail("wiki@pbnl.de");

        $this->assertEquals(6, count($allForwards));

        $this->assertContains('TestAmbrone1@pbnl.de', $wiki[0]->getForward());
    }
}
