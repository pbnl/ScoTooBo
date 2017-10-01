<?php

namespace Tests\AppBundle\UserServiceTests;

use AppBundle\Model\Entity\LDAP\PbnlAccount;
use AppBundle\Model\Services\CorruptDataInDatabaseException;
use AppBundle\Model\Services\UserRepository;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Ucsf\LdapOrmBundle\Ldap\LdapEntityManager;
use Ucsf\LdapOrmBundle\Repository\Repository;
use Symfony\Component\Validator\Validation;

class UserServiceTest extends WebTestCase
{

    public function testGetUserByGivenName()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setL("hamburg");
        $pbnlAccount->setOu("ambronen");
        $pbnlAccount->setStreet("street");
        $pbnlAccount->setPostalCode("12345");
        $pbnlAccount->setGivenName("test");
        $pbnlAccount->setCn("testcn");
        $pbnlAccount->setSn("testsn");
        $pbnlAccount->setMail("testmail@pbnl.de");
        $pbnlAccount->setTelephoneNumber("123456789");
        $pbnlAccount->setMobile("1234567890");
        $pbnlAccount->setGidNumber("123");
        $pbnlAccount->setHomeDirectory("/home/test");
        $pbnlAccount->setUidNumber("1234");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->once())->method("__call")->with(
            $this->equalTo('findOneByGivenName'),
            $this->equalTo(["test"])
            )->willReturn($pbnlAccount);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->once())->method("getRepository")->willReturn($pbnlAccountRepo);

        $userService = new UserRepository(new Logger("main"),$ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $user = $userService->getUserByGivenName("test");

        $this->assertEquals("hamburg",$user->getCity());
        $this->assertEquals("street",$user->getStreet());
        $this->assertEquals("12345",$user->getPostalCode());
        $this->assertEquals("testcn",$user->getFirstName());
        $this->assertEquals("testsn",$user->getSecondName());
        $this->assertEquals("123456789",$user->getHomePhoneNumber());
        $this->assertEquals("1234567890",$user->getMobilePhoneNumber());
        $this->assertEquals("1234",$user->getUidNumber());
    }

    public function testFindNoneExistingUser()
    {
        $this->expectException(UsernameNotFoundException::class);

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->once())->method("__call")->with(
            $this->equalTo('findOneByGivenName'),
            $this->equalTo(["test"])
        )->willReturn([]);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->once())->method("getRepository")->willReturn($pbnlAccountRepo);

        $userService = new UserRepository(new Logger("main"),$ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $userService->getUserByGivenName("test");
    }

    public function testCorruptDataInDatabaseExceptionMail()
    {
        $this->expectException(CorruptDataInDatabaseException::class);

        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setL("hamburg");
        $pbnlAccount->setOu("ambronen");
        $pbnlAccount->setStreet("street");
        $pbnlAccount->setPostalCode("12345");
        $pbnlAccount->setGivenName("test");
        $pbnlAccount->setCn("testcn");
        $pbnlAccount->setSn("testsn");
        $pbnlAccount->setMail("wrongMail.de");
        $pbnlAccount->setTelephoneNumber("123456789");
        $pbnlAccount->setMobile("1234567890");
        $pbnlAccount->setGidNumber("123");
        $pbnlAccount->setHomeDirectory("/home/test");
        $pbnlAccount->setUidNumber("1234");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->once())->method("__call")->with(
            $this->equalTo('findOneByGivenName'),
            $this->equalTo(["test"])
        )->willReturn($pbnlAccount);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->once())->method("getRepository")->willReturn($pbnlAccountRepo);

        $userRepo = new UserRepository(new Logger("main"),$ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $userRepo->getUserByGivenName("test");
    }

    public function testCorruptDataInDatabaseExceptionPLZ()
    {
        $this->expectException(CorruptDataInDatabaseException::class);

        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setL("hamburg");
        $pbnlAccount->setOu("ambronen");
        $pbnlAccount->setStreet("street");
        $pbnlAccount->setPostalCode("123w45");
        $pbnlAccount->setGivenName("test");
        $pbnlAccount->setCn("testcn");
        $pbnlAccount->setSn("testsn");
        $pbnlAccount->setMail("wrongMail@pbnl.de");
        $pbnlAccount->setTelephoneNumber("123456789");
        $pbnlAccount->setMobile("1234567890");
        $pbnlAccount->setGidNumber("123");
        $pbnlAccount->setHomeDirectory("/home/test");
        $pbnlAccount->setUidNumber("1234");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->once())->method("__call")->with(
            $this->equalTo('findOneByGivenName'),
            $this->equalTo(["test"])
        )->willReturn($pbnlAccount);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->once())->method("getRepository")->willReturn($pbnlAccountRepo);

        $userRepo = new UserRepository(new Logger("main"),$ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $userRepo->getUserByGivenName("test");
    }
}