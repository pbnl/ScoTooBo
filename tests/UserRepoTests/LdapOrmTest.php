<?php

namespace App\Tests\UserServicTest;

use App\Entity\LDAP\PbnlAccount;
use App\Entity\LDAP\PbnlMailAlias;
use App\Entity\LDAP\PosixGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Ucsf\LdapOrmBundle\Repository\Repository;

class LdapOrmTest extends KernelTestCase
{

    public function setUp():void
    {
        self::bootKernel();

    }

    public function testReadPbnlAccountORM()
    {

        $ldapEntityManager = self::$kernel->getContainer()->get("App\Model\LdapComponent\PbnlLdapEntityManager");
        $personRepository = $ldapEntityManager->getRepository(PbnlAccount::class);
        $testAmbrone = $personRepository->findByGivenName("TestAmbrone1");

        $this->assertStringContainsString('/home/testambrone1', $testAmbrone[0]->getHomeDirectory());
    }

    public function testcreateAndModDeletPbnlAccountORM()
    {

        $ldapEntityManager = self::$kernel->getContainer()->get("App\Model\LdapComponent\PbnlLdapEntityManager");
        $personRepository = $ldapEntityManager->getRepository(PbnlAccount::class);

        $newOne = new PbnlAccount();
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
        $this->assertStringContainsString('/home/random', $newOne[0]->getHomeDirectory());

        $newOne[0]->setHomeDirectory("/home/random2");
        $newOne[0]->setSn("NiceName");
        $ldapEntityManager->persist($newOne[0]);

        $newOne = $personRepository->findByGivenName("TestAccountToDelete");
        $this->assertStringContainsString('/home/random2', $newOne[0]->getHomeDirectory());
        $this->assertStringContainsString('NiceName', $newOne[0]->getSn());

        $newOne = $personRepository->findByGivenName("TestAccountToDelete");
        $ldapEntityManager->delete($newOne[0]);

        $newOne = $personRepository->findByGivenName("TestAccountToDelet");
        $this->assertEquals(0, count($newOne));
    }

    public function testPosixGroupORM()
    {

        $ldapEntityManager = self::$kernel->getContainer()->get("App\Model\LdapComponent\PbnlLdapEntityManager");
        $personRepository = $ldapEntityManager->getRepository(PosixGroup::class);
        $allGroups = $personRepository->findAll();
        $ambronen = $personRepository->findByCn("ambronen");

        $this->assertStringContainsString('stammGroup', $ambronen[0]->getDescription());
    }
}
