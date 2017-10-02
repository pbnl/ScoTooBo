<?php

namespace Tests\AppBundle\UserServicTest;

use AppBundle\Model\Entity\LDAP\PbnlAccount;
use AppBundle\Model\Entity\LDAP\PbnlMailAlias;
use AppBundle\Model\Entity\LDAP\PosixGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Ucsf\LdapOrmBundle\Repository\Repository;

class LdapOrmTest extends KernelTestCase
{

    /** @var  $container Container */
    private $container;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
    }

    public function testReadPbnlAccountORM()
    {

        $ldapEntityManager = $this->container->get("ldapEntityManager");
        $personRepository = $ldapEntityManager->getRepository(PbnlAccount::class);
        $testAmbrone = $personRepository->findByGivenName("TestAmbrone1");

        $this->assertContains('/home/testambrone1', $testAmbrone[0]->getHomeDirectory());
    }

    public function testcreateAndModDeletPbnlAccountORM()
    {

        $ldapEntityManager = $this->container->get("ldapEntityManager");
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

        $newOne = $personRepository->findByGivenName("TestAccountToDelet");
        $this->assertEquals(0, count($newOne));
    }

    public function testPosixGroupORM()
    {

        $ldapEntityManager = $this->container->get("ldapEntityManager");
        $personRepository = $ldapEntityManager->getRepository(PosixGroup::class);
        $allGroups = $personRepository->findAll();
        $ambronen = $personRepository->findByCn("ambronen");

        $this->assertContains('stammGroup', $ambronen[0]->getDescription());
    }

    public function testpbnlMailAliasORM()
    {

        $ldapEntityManager = $this->container->get("ldapEntityManager");
        $personRepository = $ldapEntityManager->getRepository(PbnlMailAlias::class);
        $wiki = $personRepository->findByMail("wiki@pbnl.de");

        $this->assertContains('TestAmbrone1@pbnl.de', $wiki[0]->getForward());
    }
}
