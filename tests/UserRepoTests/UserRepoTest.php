<?php

namespace App\Tests\UserServiceTests;

use App\Entity\LDAP\PbnlAccount;
use App\Entity\LDAP\PosixGroup;
use App\Model\LdapComponent\PbnlLdapEntityManager;
use App\Model\LdapComponent\Repositories\Repository;
use App\Model\Services\CorruptDataInDatabaseException;
use App\Model\Services\GroupRepository;
use App\Model\Services\UserAlreadyExistException;
use App\Model\Services\UserDoesNotExistException;
use App\Model\Services\UserLazyLoader;
use App\Model\Services\UserNotUniqueException;
use App\Model\Services\UserRepository;
use App\Model\SSHA;
use App\Model\User;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
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
        $pbnlAccount->setGivenName("testGivenName");
        $pbnlAccount->setOu("testOu");

        $groupRepo = $this->createMock(GroupRepository::class);

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
        $pbnlAccount->setGivenName("testGivenName");
        $pbnlAccount->setOu("testOu");

        $groupRepo = $this->createMock(GroupRepository::class);

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
        $pbnlAccount->setGivenName("testGivenName");
        $pbnlAccount->setOu("testOu");

        $groupRepo = $this->createMock(GroupRepository::class);

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
        $pbnlAccount->setGivenName("testGivenName");
        $pbnlAccount->setOu("testOu");

        $groupRepo = $this->createMock(GroupRepository::class);

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
        $pbnlAccount->setGivenName("testGivenName");
        $pbnlAccount->setOu("testOu");

        $groupRepo = $this->createMock(GroupRepository::class);

        $userRepo = new UserRepository(new Logger("main"),
            $this->mockLdapEntityManager($pbnlAccount),
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $user = $userRepo->getUserByUid("test");
        $this->assertEquals("tef_efg", $user->getUid());
    }

    /**
     *
     */
    public function testAddUser()
    {
        $ssha = SSHA::sshaPasswordGenWithGivenSalt("password","12345678");
        $expectedPbnlAccount = new PbnlAccount();
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

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->at(0))
            ->method("getRepository")
            ->with($this->equalTo(PosixGroup::class))
            ->willReturn($pbnlAccountRepo);
        $ldapEntityManager->expects($this->at(1))
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

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->at(0))
            ->method("getRepository")
            ->with($this->equalTo(PosixGroup::class))
            ->willReturn($pbnlAccountRepo);
        $ldapEntityManager->expects($this->at(1))
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

        $groupRepo = $this->createMock(GroupRepository::class);

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->withConsecutive(
                [$this->equalTo(PosixGroup::class)],
                [$this->equalTo(PbnlAccount::class)],
                [$this->equalTo(PbnlAccount::class)])
            ->willReturnOnConsecutiveCalls($groupRepo, $pbnlAccountRepo, $pbnlAccountRepo);

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
        $oldPbnlAccount->setUserPassword("");

        $newPbnlAccount = new PbnlAccount();
        $newPbnlAccount->setL("teststadtNew");
        $newPbnlAccount->setUid("testuid");
        $newPbnlAccount->setGidNumber("501");
        $newPbnlAccount->setHomeDirectory("/home/testuid");
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

        $groupRepo = $this->createMock(GroupRepository::class);

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->withConsecutive(
                [$this->equalTo(PosixGroup::class)],
                [$this->equalTo(PbnlAccount::class)])
            ->willReturnOnConsecutiveCalls($groupRepo, $pbnlAccountRepo);
        $ldapEntityManager->expects($this->once())
            ->method("persist")
            ->with($this->equalTo($newPbnlAccount));

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

        $groupRepo = $this->createMock(GroupRepository::class);

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->at(0))
            ->method("getRepository")
            ->with($this->equalTo(PosixGroup::class))
            ->willReturn($groupRepo);
        $ldapEntityManager->expects($this->at(1))
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);

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

        $groupRepo = $this->createMock(GroupRepository::class);

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->at(0))
            ->method("getRepository")
            ->with($this->equalTo(PosixGroup::class))
            ->willReturn($groupRepo);
        $ldapEntityManager->expects($this->at(1))
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);

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

        $groupRepo = $this->createMock(GroupRepository::class);

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->at(0))
            ->method("getRepository")
            ->with($this->equalTo(PosixGroup::class))
            ->willReturn($groupRepo);
        $ldapEntityManager->expects($this->at(1))
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);

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
        $toDeletePbnlAccount->setUidNumber("0");
        $toDeletePbnlAccount->setUserPassword($ssha);

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["0"])])
            ->willReturnOnConsecutiveCalls([""],[""]);

        $groupRepo = $this->createMock(GroupRepository::class);

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->at(0))
            ->method("getRepository")
            ->with($this->equalTo(PosixGroup::class))
            ->willReturn($groupRepo);
        $ldapEntityManager->expects($this->at(1))
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);
        $ldapEntityManager->expects($this->once())
            ->method("delete")
            ->with($this->equalTo($toDeletePbnlAccount));

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
        $toDeletePbnlAccount->setUidNumber("0");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["0"])])
            ->willReturnOnConsecutiveCalls([],[]);

        $groupRepo = $this->createMock(GroupRepository::class);

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->at(0))
            ->method("getRepository")
            ->with($this->equalTo(PosixGroup::class))
            ->willReturn($groupRepo);
        $ldapEntityManager->expects($this->at(1))
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);
        $ldapEntityManager->expects($this->never())
            ->method("delete");

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
        $toDeletePbnlAccount->setUidNumber("0");

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->withConsecutive(
                [$this->equalTo('findByUid'), $this->equalTo(["testuid"])],
                [$this->equalTo('findByUidNumber'), $this->equalTo(["0"])])
            ->willReturnOnConsecutiveCalls(["",""],[]);

        $groupRepo = $this->createMock(GroupRepository::class);

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->at(0))
            ->method("getRepository")
            ->with($this->equalTo(PosixGroup::class))
            ->willReturn($groupRepo);
        $ldapEntityManager->expects($this->at(1))
            ->method("getRepository")
            ->with($this->equalTo(PbnlAccount::class))
            ->willReturn($pbnlAccountRepo);
        $ldapEntityManager->expects($this->never())
            ->method("delete");

        $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $userRepo->removeUser($toDeleteUser);
    }

    public function testFindUserByDn()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setUid("uid");
        $pbnlAccount->setGivenName("testGivenName");
        $pbnlAccount->setOu("testOu");

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->once())
            ->method("retrieveByDn")
            ->with(
                $this->equalTo('testDn'),
                $this->equalTo("App\Entity\LDAP\PbnlAccount")
            )
            ->willReturn([$pbnlAccount]);

        $expectedUser = new User("uid", "", "", new UserLazyLoader($ldapEntityManager));
        $expectedUser->setGivenName("testGivenName");
        $expectedUser->setStamm("testOu");
        $expectedUser->setDn("givenName=testGivenName,ou=testOu,ou=People,dc=pbnl,dc=de");

        $groupRepo = $this->createMock(GroupRepository::class);
        $groupRepo->expects($this->any())
            ->method("findAll")
            ->willReturn([]);

        $userRepo = $userRepo = new UserRepository(new Logger("main"),
            $ldapEntityManager,
            Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator(),
            $groupRepo);

        $actualUser = $userRepo->findUserByDn("testDn");
        $this->assertEquals($expectedUser, $actualUser);
    }

    /**
     * Creates a mocked ldapEntityManager witch is able to return
     *  a some users
     *  some groups (the ROLES of the user)
     *
     *
     * @param $pbnlAccounts
     * @return \PHPUnit_Framework_MockObject_MockObject|PbnlLdapEntityManager
     */
    public function mockLdapEntityManager($pbnlAccounts) {

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->with(
                $this->equalTo('findOneByUid'),
                $this->equalTo(["test"]))
            ->willReturn($pbnlAccounts);

        $posixGroupRepo = $this->createMock(Repository::class);

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->at(0))
            ->method("getRepository")
            ->with($this->equalTo(PosixGroup::class))
            ->willReturn($posixGroupRepo);
        $ldapEntityManager->expects($this->at(1))
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
