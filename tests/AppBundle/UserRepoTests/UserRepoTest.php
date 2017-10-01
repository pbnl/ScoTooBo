<?php

namespace Tests\AppBundle\UserServiceTests;

use AppBundle\Model\Entity\LDAP\PbnlAccount;
use AppBundle\Model\Entity\LDAP\PosixGroup;
use AppBundle\Model\Services\CorruptDataInDatabaseException;
use AppBundle\Model\Services\UserRepository;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Ucsf\LdapOrmBundle\Ldap\LdapEntityManager;
use Ucsf\LdapOrmBundle\Repository\Repository;
use Symfony\Component\Validator\Validation;

class UserRepoTest extends WebTestCase
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


        $userService = new UserRepository(new Logger("main"), $this->mockLdapEntityManager($pbnlAccount, []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
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


        $userService = new UserRepository(new Logger("main"), $this->mockLdapEntityManager([], [], false), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
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


        $userRepo = new UserRepository(new Logger("main"),$this->mockLdapEntityManager($pbnlAccount, []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
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


        $userRepo = new UserRepository(new Logger("main"), $this->mockLdapEntityManager($pbnlAccount, []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $userRepo->getUserByGivenName("test");
    }

    /**
     * Creates a mocked ldapEntityManager witch is able to return
     *  a user
     *  some groups (the ROLES of the user)
     *
     * $userExists must be false of the user does not exist
     *
     * @param $pbnlAccount
     * @param $groups
     * @param bool $userExists
     * @return \PHPUnit_Framework_MockObject_MockObject|LdapEntityManager
     */
    public function mockLdapEntityManager($pbnlAccount, $groups, $userExists = true) {

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())->method("__call")->with(
            $this->equalTo('findOneByGivenName'),
            $this->equalTo(["test"])
        )->willReturn($pbnlAccount);
        $groupRepo = $this->createMock(Repository::class);
        $groupRepo->expects($this->any())->method("findAll")->willReturn($groups);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->at(0))->method("getRepository")->with($this->equalTo(PbnlAccount::class))->willReturn($pbnlAccountRepo);

        if($userExists == true) {
            $ldapEntityManager->expects($this->at(1))->method("getRepository")->with(
                $this->equalTo(PosixGroup::class)
            )->willReturn($groupRepo);
        }
        return $ldapEntityManager;
    }
}
