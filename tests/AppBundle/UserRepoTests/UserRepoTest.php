<?php

namespace Tests\AppBundle\UserServiceTests;

use AppBundle\Model\Entity\LDAP\PbnlAccount;
use AppBundle\Model\Entity\LDAP\PosixGroup;
use AppBundle\Model\Services\CorruptDataInDatabaseException;
use AppBundle\Model\Services\GroupRepository;
use AppBundle\Model\Services\UserAlreadyExistException;
use AppBundle\Model\Services\UserDoesNotExistException;
use AppBundle\Model\Services\UserNotUniqueException;
use AppBundle\Model\Services\UserRepository;
use AppBundle\Model\SSHA;
use AppBundle\Model\User;
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
        $pbnlAccount->setUid("test");
        $pbnlAccount->setCn("testcn");
        $pbnlAccount->setSn("testsn");
        $pbnlAccount->setMail("testmail@pbnl.de");
        $pbnlAccount->setTelephoneNumber("123456789");
        $pbnlAccount->setMobile("1234567890");
        $pbnlAccount->setGidNumber("123");
        $pbnlAccount->setHomeDirectory("/home/test");
        $pbnlAccount->setUidNumber("1234");


        $groupRepo = $this->createMock(GroupRepository::class);
        $groupRepo->expects($this->once())
            ->method("findAll")
            ->willReturn([]);

        $userService = new UserRepository(new Logger("main"),
            $this->mockLdapEntityManager($pbnlAccount),
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);
        $user = $userService->getUserByUid("test");

        $this->assertEquals("hamburg",$user->getCity());
        $this->assertEquals("street",$user->getStreet());
        $this->assertEquals("12345",$user->getPostalCode());
        $this->assertEquals("testcn",$user->getFirstName());
        $this->assertEquals("testsn",$user->getLastName());
        $this->assertEquals("123456789",$user->getHomePhoneNumber());
        $this->assertEquals("1234567890",$user->getMobilePhoneNumber());
        $this->assertEquals("1234",$user->getUidNumber());
    }

    public function testFindNoneExistingUser()
    {
        $this->expectException(UsernameNotFoundException::class);

        $groupRepo = $this->createMock(GroupRepository::class);
        $groupRepo->expects($this->never())
            ->method("findAll")
            ->willReturn([]);

        $userService = new UserRepository(new Logger("main"),
            $this->mockLdapEntityManager([]),
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);
        $userService->getUserByUid("test");
    }

    public function testCorruptDataInDatabaseExceptionMail()
    {
        $this->expectException(CorruptDataInDatabaseException::class);

        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setMail("wrongMail.de");

        $groupRepo = $this->createMock(GroupRepository::class);
        $groupRepo->expects($this->once())
            ->method("findAll")
            ->willReturn([]);

        $userRepo = new UserRepository(new Logger("main"),
            $this->mockLdapEntityManager($pbnlAccount),
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);
        $userRepo->getUserByUid("test");
    }

    public function testCorruptDataInDatabaseExceptionUid()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setUid("Tefefg");

        $groupRepo = $this->createMock(GroupRepository::class);
        $groupRepo->expects($this->once())
            ->method("findAll")
            ->willReturn([]);

        $userRepo = new UserRepository(new Logger("main"),
            $this->mockLdapEntityManager($pbnlAccount),
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $user = $userRepo->getUserByUid("test");
        $this->assertEquals("tefefg", $user->getUid());
    }
    public function testCorruptDataInDatabaseExceptionUid2()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setUid("teäfefg");

        $groupRepo = $this->createMock(GroupRepository::class);
        $groupRepo->expects($this->once())
            ->method("findAll")
            ->willReturn([]);

        $userRepo = new UserRepository(new Logger("main"),
            $this->mockLdapEntityManager($pbnlAccount),
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $user = $userRepo->getUserByUid("test");
        $this->assertEquals("teaefefg", $user->getUid());
    }
    public function testCorruptDataInDatabaseExceptionUid3()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setUid("tefßefg");

        $groupRepo = $this->createMock(GroupRepository::class);
        $groupRepo->expects($this->once())
            ->method("findAll")
            ->willReturn([]);

        $userRepo = new UserRepository(new Logger("main"),
            $this->mockLdapEntityManager($pbnlAccount),
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $user = $userRepo->getUserByUid("test");
        $this->assertEquals("tefssefg", $user->getUid());
    }
    public function testCorruptDataInDatabaseExceptionUid4()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setUid("tef efg");

        $groupRepo = $this->createMock(GroupRepository::class);
        $groupRepo->expects($this->once())
            ->method("findAll")
            ->willReturn([]);

        $userRepo = new UserRepository(new Logger("main"),
            $this->mockLdapEntityManager($pbnlAccount),
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $user = $userRepo->getUserByUid("test");
        $this->assertEquals("tef_efg", $user->getUid());
    }

    public function testCorruptDataInDatabaseExceptionPLZ()
    {
        $this->expectException(CorruptDataInDatabaseException::class);

        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setPostalCode("123w45");

        $groupRepo = $this->createMock(GroupRepository::class);
        $groupRepo->expects($this->once())
            ->method("findAll")
            ->willReturn([]);

        $userRepo = new UserRepository(new Logger("main"),
            $this->mockLdapEntityManager($pbnlAccount),
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userRepo->getUserByUid("test");
    }

    /**
     *
     */
    public function testAddUser()
    {
        $ssha = SSHA::sshaPasswordGenWithGivenSalt("password","12345678");
        $expectedPbnlAccount = new PbnlAccount();
        $expectedPbnlAccount->setObjectClass(["inetOrgPerson","posixAccount","pbnlAccount"]);
        $expectedPbnlAccount->setL("hamburg");
        $expectedPbnlAccount->setOu("ambronen");
        $expectedPbnlAccount->setStreet("street");
        $expectedPbnlAccount->setPostalCode("12345");
        $expectedPbnlAccount->setGivenName("testgiven");
        $expectedPbnlAccount->setUid("testuid");
        $expectedPbnlAccount->setCn("testcn");
        $expectedPbnlAccount->setSn("testsn");
        $expectedPbnlAccount->setMail("testmail@pbnl.de");
        $expectedPbnlAccount->setTelephoneNumber("123456789");
        $expectedPbnlAccount->setMobile("1234567890");
        $expectedPbnlAccount->setGidNumber("501");
        $expectedPbnlAccount->setHomeDirectory("/home/testuid");
        $expectedPbnlAccount->setUidNumber("8");
        $expectedPbnlAccount->setUserPassword($ssha);

        $user = new User("test", SSHA::sshaGetHash($ssha), SSHA::sshaGetSalt($ssha), []);
        $user->setDn("givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de");
        $user->setCity("hamburg");
        $user->setFirstName("testcn");
        $user->setLastName("testsn");
        $user->setUidNumber(1234);
        $user->setMail("testmail@pbnl.de");
        $user->setGivenName("testgiven");
        $user->setUid("testuid");
        $user->setPostalCode("12345");
        $user->setMobilePhoneNumber("1234567890");
        $user->setStreet("street");
        $user->setHomePhoneNumber("123456789");
        $user->setStamm("ambronen");

        $user1 = new User("test", "hash", "salt", []);
        $user1->setUidNumber(3);
        $user2 = new User("test", "hash", "salt", []);
        $user2->setUidNumber(7);
        $user3 = new User("test", "hash", "salt", []);
        $user3->setUidNumber(-10);
        $uidNumberUsers = [$user1, $user2, $user3];

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["1234"])])
            ->willReturnOnConsecutiveCalls([],[]);
        $pbnlAccountRepo->expects($this->once())
            ->method("findAll")
            ->willReturn($uidNumberUsers);

        $groupRepo = $this->createMock(GroupRepository::class);
        $groupRepo->expects($this->any())
            ->method("findAll")
            ->willReturn([]);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);

        $ldapEntityManager->expects($this->once())
            ->method("persist")
            ->with($this->equalTo($expectedPbnlAccount));

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userBack = $userRepo->addUser($user);
        $this->assertEquals($user,$userBack);
    }

    public function testAddUserUserAlreadyExistExceptionUid()
    {
        $this->expectException(UserAlreadyExistException::class);

        $expectedPbnlAccount = new PbnlAccount();
        $expectedPbnlAccount->setObjectClass(["inetOrgPerson","posixAccount","pbnlAccount"]);
        $expectedPbnlAccount->setL("hamburg");
        $expectedPbnlAccount->setOu("ambronen");
        $expectedPbnlAccount->setStreet("street");
        $expectedPbnlAccount->setPostalCode("12345");
        $expectedPbnlAccount->setGivenName("testgiven");
        $expectedPbnlAccount->setUid("testuid");
        $expectedPbnlAccount->setCn("testcn");
        $expectedPbnlAccount->setSn("testsn");
        $expectedPbnlAccount->setMail("testmail@pbnl.de");
        $expectedPbnlAccount->setTelephoneNumber("123456789");
        $expectedPbnlAccount->setMobile("1234567890");
        $expectedPbnlAccount->setGidNumber("501");
        $expectedPbnlAccount->setHomeDirectory("/home/testuid");
        $expectedPbnlAccount->setUidNumber("8");

        $user = new User("test", "hash", "salt", []);
        $user->setDn("givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de");
        $user->setCity("hamburg");
        $user->setFirstName("testcn");
        $user->setLastName("testsn");
        $user->setUidNumber(1234);
        $user->setMail("testmail@pbnl.de");
        $user->setGivenName("testgiven");
        $user->setUid("testuid");
        $user->setPostalCode("12345");
        $user->setMobilePhoneNumber("1234567890");
        $user->setStreet("street");
        $user->setHomePhoneNumber("123456789");
        $user->setStamm("ambronen");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["1234"])])
            ->willReturnOnConsecutiveCalls($expectedPbnlAccount,[]);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);

        $groupRepo = $this->createMock(GroupRepository::class);

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userBack = $userRepo->addUser($user);
        $this->assertEquals($user,$userBack);
    }

    public function testAddUserUserAlreadyExistExceptionUidNumber()
    {
        $this->expectException(UserAlreadyExistException::class);

        /** @var PbnlAccount $expectedPbnlAccount */
        $expectedPbnlAccount = new PbnlAccount();
        $expectedPbnlAccount->setObjectClass(["inetOrgPerson","posixAccount","pbnlAccount"]);
        $expectedPbnlAccount->setL("hamburg");
        $expectedPbnlAccount->setOu("ambronen");
        $expectedPbnlAccount->setStreet("street");
        $expectedPbnlAccount->setPostalCode("12345");
        $expectedPbnlAccount->setGivenName("testgiven");
        $expectedPbnlAccount->setUid("testuid");
        $expectedPbnlAccount->setCn("testcn");
        $expectedPbnlAccount->setSn("testsn");
        $expectedPbnlAccount->setMail("testmail@pbnl.de");
        $expectedPbnlAccount->setTelephoneNumber("123456789");
        $expectedPbnlAccount->setMobile("1234567890");
        $expectedPbnlAccount->setGidNumber("501");
        $expectedPbnlAccount->setHomeDirectory("/home/testuid");
        $expectedPbnlAccount->setUidNumber("8");

        $user = new User("test", "hash", "salt", []);
        $user->setDn("givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de");
        $user->setCity("hamburg");
        $user->setFirstName("testcn");
        $user->setLastName("testsn");
        $user->setUidNumber(1234);
        $user->setMail("testmail@pbnl.de");
        $user->setGivenName("testgiven");
        $user->setUid("testuid");
        $user->setPostalCode("12345");
        $user->setMobilePhoneNumber("1234567890");
        $user->setStreet("street");
        $user->setHomePhoneNumber("123456789");
        $user->setStamm("ambronen");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["1234"])])
            ->willReturnOnConsecutiveCalls([],$expectedPbnlAccount);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->withConsecutive(
                [$this->equalTo(PbnlAccount::class)],
                [$this->equalTo(PbnlAccount::class)])
            ->willReturnOnConsecutiveCalls($pbnlAccountRepo, $pbnlAccountRepo);

        $groupRepo = $this->createMock(GroupRepository::class);

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userBack = $userRepo->addUser($user);
        $this->assertEquals($user,$userBack);
    }

    public function testUpdateUser()
    {
        $sshaPassword = SSHA::sshaPasswordGenWithGivenSalt("passwort","12345678");

        $oldPbnlAccount = new PbnlAccount();
        $oldPbnlAccount->setL("teststadtOld");
        $oldPbnlAccount->setUid("testuid");
        $oldPbnlAccount->setUserPassword();

        $newPbnlAccount = new PbnlAccount();
        $newPbnlAccount->setL("teststadtNew");
        $newPbnlAccount->setUid("testuid");
        $newPbnlAccount->setGidNumber("501");
        $newPbnlAccount->setHomeDirectory("/home/testuid");
        $newPbnlAccount->setObjectClass(["inetOrgPerson","posixAccount","pbnlAccount"]);
        $newPbnlAccount->setUserPassword($sshaPassword);


        $newUser = new User("testuid",
            SSHA::sshaGetHash($sshaPassword),
            SSHA::sshaGetSalt($sshaPassword),
            []);
        $newUser->setCity("teststadtNew");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])])
            ->willReturnOnConsecutiveCalls($oldPbnlAccount);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->withConsecutive(
                [$this->equalTo(PbnlAccount::class)])
            ->willReturnOnConsecutiveCalls($pbnlAccountRepo);
        $ldapEntityManager->expects($this->once())
            ->method("persist")
            ->with($this->equalTo($newPbnlAccount));

        $groupRepo = $this->createMock(GroupRepository::class);

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userRepo->updateUser($newUser);
    }

    public function testUpdateUserUserDoesNotExistException()
    {
        $this->expectException(UserDoesNotExistException::class);

        $newUser = new User("testuid", "hash", "salt", []);
        $newUser->setCity("teststadtNew");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["0"])])
            ->willReturnOnConsecutiveCalls([],[]);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);

        $groupRepo = $this->createMock(GroupRepository::class);

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userRepo->updateUser($newUser);
    }

    public function testUpdateUserUserNotUniqueException()
    {
        $this->expectException(UserNotUniqueException::class);

        $newUser = new User("testuid", "hash", "salt", []);
        $newUser->setCity("teststadtNew");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["0"])])
            ->willReturnOnConsecutiveCalls(["",""],["",""]);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);

        $groupRepo = $this->createMock(GroupRepository::class);

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userRepo->updateUser($newUser);
    }

    public function testGetNewUidNumber()
    {
        $user1 = new User("test", "hash", "salt", []);
        $user1->setUidNumber(3);
        $user2 = new User("test", "hash", "salt", []);
        $user2->setUidNumber(7);
        $user3 = new User("test", "hash", "salt", []);
        $user3->setUidNumber(-10);
        $uidNumberUsers = [$user1, $user2, $user3];

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->once())
            ->method("findAll")
            ->willReturn($uidNumberUsers);
        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->once())
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);

        $groupRepo = $this->createMock(GroupRepository::class);

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $uidNumber = $this->invokeMethod($userRepo,"getNewUidNumber",[]);
        $this->assertEquals(8,$uidNumber);
    }

    public function testRemoveUser()
    {
        $ssha = SSHA::sshaPasswordGenWithGivenSalt("password","12345678");
        $toDeleteUser = new User("testuid", SSHA::sshaGetHash($ssha), SSHA::sshaGetSalt($ssha), []);

        $toDeletePbnlAccount = new PbnlAccount();
        $toDeletePbnlAccount->setUid("testuid");
        $toDeletePbnlAccount->setGidNumber("501");
        $toDeletePbnlAccount->setHomeDirectory("/home/testuid");
        $toDeletePbnlAccount->setObjectClass(["inetOrgPerson","posixAccount","pbnlAccount"]);
        $toDeletePbnlAccount->setUidNumber("0");
        $toDeletePbnlAccount->setUserPassword($ssha);

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["0"])])
            ->willReturnOnConsecutiveCalls([""],[""]);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);
        $ldapEntityManager->expects($this->once())
            ->method("delete")
            ->with($this->equalTo($toDeletePbnlAccount));

        $groupRepo = $this->createMock(GroupRepository::class);

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userRepo->removeUser($toDeleteUser);
    }

    public function testRemoveUserUserDoesNotExist()
    {
        $toDeleteUser = new User("testuid", "hash", "salt", []);

        $toDeletePbnlAccount = new PbnlAccount();
        $toDeletePbnlAccount->setUid("testuid");
        $toDeletePbnlAccount->setGidNumber("501");
        $toDeletePbnlAccount->setHomeDirectory("/home/testuid");
        $toDeletePbnlAccount->setObjectClass(["inetOrgPerson","posixAccount","pbnlAccount"]);
        $toDeletePbnlAccount->setUidNumber("0");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["0"])])
            ->willReturnOnConsecutiveCalls([],[]);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);
        $ldapEntityManager->expects($this->never())
            ->method("delete");

        $groupRepo = $this->createMock(GroupRepository::class);

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userRepo->removeUser($toDeleteUser);
    }

    public function testRemoveUserUserNotUniqueException()
    {
        $this->expectException(UserNotUniqueException::class);

        $toDeleteUser = new User("testuid", "hash", "salt", []);

        $toDeletePbnlAccount = new PbnlAccount();
        $toDeletePbnlAccount->setUid("testuid");
        $toDeletePbnlAccount->setGidNumber("501");
        $toDeletePbnlAccount->setHomeDirectory("/home/testuid");
        $toDeletePbnlAccount->setObjectClass(["inetOrgPerson","posixAccount","pbnlAccount"]);
        $toDeletePbnlAccount->setUidNumber("0");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["0"])])
            ->willReturnOnConsecutiveCalls(["",""],[]);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);
        $ldapEntityManager->expects($this->never())
            ->method("delete");

        $groupRepo = $this->createMock(GroupRepository::class);

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userRepo->removeUser($toDeleteUser);
    }

    /**
     * Creates a mocked ldapEntityManager witch is able to return
     *  a user
     *  some groups (the ROLES of the user)
     *
     *
     * @param $pbnlAccount
     * @return \PHPUnit_Framework_MockObject_MockObject|LdapEntityManager
     */
    public function mockLdapEntityManager($pbnlAccount) {

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->with(
                $this->equalTo('findOneByUid'),
                $this->equalTo(["test"]))
            ->willReturn($pbnlAccount);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);

        return $ldapEntityManager;
    }

    /**
     * Lets you call a private or protected function
     *
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
