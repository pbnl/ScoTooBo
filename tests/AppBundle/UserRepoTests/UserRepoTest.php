<?php

namespace Tests\AppBundle\UserServiceTests;

use AppBundle\Model\Entity\LDAP\PbnlAccount;
use AppBundle\Model\Entity\LDAP\PosixGroup;
use AppBundle\Model\Services\CorruptDataInDatabaseException;
use AppBundle\Model\Services\UserAlreadyExistException;
use AppBundle\Model\Services\UserDoesNotExistException;
use AppBundle\Model\Services\UserNotUniqueException;
use AppBundle\Model\Services\UserRepository;
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


        $userService = new UserRepository(new Logger("main"), $this->mockLdapEntityManager($pbnlAccount, []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
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


        $userService = new UserRepository(new Logger("main"), $this->mockLdapEntityManager([], []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $userService->getUserByUid("test");
    }

    public function testCorruptDataInDatabaseExceptionMail()
    {
        $this->expectException(CorruptDataInDatabaseException::class);

        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setMail("wrongMail.de");

        $userRepo = new UserRepository(new Logger("main"),$this->mockLdapEntityManager($pbnlAccount, []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $userRepo->getUserByUid("test");
    }

    public function testCorruptDataInDatabaseExceptionUid()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setUid("Tefefg");

        $userRepo = new UserRepository(new Logger("main"),$this->mockLdapEntityManager($pbnlAccount, []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $user = $userRepo->getUserByUid("test");
        $this->assertEquals("tefefg", $user->getUid());
    }
    public function testCorruptDataInDatabaseExceptionUid2()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setUid("teäfefg");

        $userRepo = new UserRepository(new Logger("main"),$this->mockLdapEntityManager($pbnlAccount, []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $user = $userRepo->getUserByUid("test");
        $this->assertEquals("teaefefg", $user->getUid());
    }
    public function testCorruptDataInDatabaseExceptionUid3()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setUid("tefßefg");

        $userRepo = new UserRepository(new Logger("main"),$this->mockLdapEntityManager($pbnlAccount, []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $user = $userRepo->getUserByUid("test");
        $this->assertEquals("tefssefg", $user->getUid());
    }
    public function testCorruptDataInDatabaseExceptionUid4()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setUid("tef efg");

        $userRepo = new UserRepository(new Logger("main"),$this->mockLdapEntityManager($pbnlAccount, []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $user = $userRepo->getUserByUid("test");
        $this->assertEquals("tef_efg", $user->getUid());
    }

    public function testCorruptDataInDatabaseExceptionPLZ()
    {
        $this->expectException(CorruptDataInDatabaseException::class);

        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setPostalCode("123w45");

        $userRepo = new UserRepository(new Logger("main"), $this->mockLdapEntityManager($pbnlAccount, []), Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $userRepo->getUserByUid("test");
    }

    /**
     *
     */
    public function testAddUser()
    {
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

        $groupRepo = $this->createMock(Repository::class);
        $groupRepo->expects($this->any())
            ->method("findAll")
            ->willReturn([]);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->withConsecutive(
                [$this->equalTo(PbnlAccount::class)],
                [$this->equalTo(PbnlAccount::class)],
                [$this->equalTo(PbnlAccount::class)],
                [$this->equalTo(PosixGroup::class)])
            ->willReturnOnConsecutiveCalls($pbnlAccountRepo, $pbnlAccountRepo, $groupRepo);
        $ldapEntityManager->expects($this->once())
            ->method("persist")
            ->with($this->equalTo($expectedPbnlAccount));

        $userRepo = new UserRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());

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
            ->withConsecutive(
                [$this->equalTo(PbnlAccount::class)])
            ->willReturnOnConsecutiveCalls($pbnlAccountRepo);

        $userRepo = new UserRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());

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
                [$this->equalTo(PbnlAccount::class)])
            ->willReturnOnConsecutiveCalls($pbnlAccountRepo);

        $userRepo = new UserRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());

        $userBack = $userRepo->addUser($user);
        $this->assertEquals($user,$userBack);
    }

    public function testupdateUser()
    {
        $oldPbnlAccount = new PbnlAccount();
        $oldPbnlAccount->setL("teststadtOld");
        $oldPbnlAccount->setUid("testuid");

        $newPbnlAccount = new PbnlAccount();
        $newPbnlAccount->setL("teststadtNew");
        $newPbnlAccount->setUid("testuid");
        $newPbnlAccount->setGidNumber("501");
        $newPbnlAccount->setHomeDirectory("/home/testuid");
        $newPbnlAccount->setObjectClass(["inetOrgPerson","posixAccount","pbnlAccount"]);


        $newUser = new User("testuid", "hash", "salt", []);
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

        $userRepo = new UserRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());

        $userRepo->updateUser($newUser);
    }

    public function testupdateUserUserDoesNotExistException()
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
            ->withConsecutive(
                [$this->equalTo(PbnlAccount::class)])
            ->willReturnOnConsecutiveCalls($pbnlAccountRepo);

        $userRepo = new UserRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());

        $userRepo->updateUser($newUser);
    }

    public function testupdateUserUserNotUniqueException()
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
            ->withConsecutive(
                [$this->equalTo(PbnlAccount::class)])
            ->willReturnOnConsecutiveCalls($pbnlAccountRepo);

        $userRepo = new UserRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());

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

        $userRepo = new UserRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());
        $uidNumber = $this->invokeMethod($userRepo,"getNewUidNumber",[]);
        $this->assertEquals(8,$uidNumber);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|LdapEntityManager
     */
    public function mockLdapEntityManager($pbnlAccount, $groups) {

        $pbnlAccountRepo = $this->createMock(Repository::class);
        $pbnlAccountRepo->expects($this->any())
            ->method("__call")
            ->with(
                $this->equalTo('findOneByUid'),
                $this->equalTo(["test"]))
            ->willReturn($pbnlAccount);

        $groupRepo = $this->createMock(Repository::class);
        $groupRepo->expects($this->any())
            ->method("findAll")
            ->willReturn($groups);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->withConsecutive([$this->equalTo(PbnlAccount::class)],[$this->equalTo(PosixGroup::class)])
            ->willReturnOnConsecutiveCalls($pbnlAccountRepo, $groupRepo);

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
