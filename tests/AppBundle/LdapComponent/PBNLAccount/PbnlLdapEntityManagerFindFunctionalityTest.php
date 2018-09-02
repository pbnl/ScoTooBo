<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 15.03.18
 * Time: 21:02
 */

namespace Tests\AppBundle\LdapComponent;


use AppBundle\Entity\LDAP\PbnlAccount;
use AppBundle\Model\LdapComponent\PbnlLdapEntityManager;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class PbnlLdapEntityManagerFindFunctionalityTest extends TestCase
{

    private $ldapConnectionConfig = array();

    public function setUp()
    {
        $this->ldapConnectionConfig["uri"] = "127.0.0.1";
        $this->ldapConnectionConfig["use_tls"] = true;
        $this->ldapConnectionConfig["password"] = "admin";
        $this->ldapConnectionConfig["bind_dn"] = "cn=admin,dc=pbnl,dc=de";
        $this->ldapConnectionConfig["base_dn"] = "dc=pbnl,dc=de";
    }

    public function testRetriveOnePbnlAccountByGivenName()
    {
        $expectedPbnlAccount = new PbnlAccount();
        $expectedPbnlAccount->setGivenName("TestAmbrone1");
        $expectedPbnlAccount->setUid("testambrone1");
        $expectedPbnlAccount->setSn("ef");
        $expectedPbnlAccount->setCn("TestAmbrone1");
        $expectedPbnlAccount->setUidNumber("814");
        $expectedPbnlAccount->setMail("TestAmbrone1@pbnl.de");
        $expectedPbnlAccount->setUserPassword("{ssha}VxyzFlqfW+MfaG3DghRX0z++sj4JXkamqKVBUg==");
        $expectedPbnlAccount->setDn("givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de");
        $expectedPbnlAccount->setPostalCode("5698");
        $expectedPbnlAccount->setHomeDirectory("/home/TestAmbrone1");
        $expectedPbnlAccount->setL("0");
        $expectedPbnlAccount->setStreet("0");
        $expectedPbnlAccount->setTelephoneNumber("0");
        $expectedPbnlAccount->setMobile("0");

        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("givenName","TestAmbrone1");

        $this->assertEquals($expectedPbnlAccount, $account);
    }

    public function testRetiriveOnePbnlAccountByUidNumber()
    {
        $expectedPbnlAccount = new PbnlAccount();
        $expectedPbnlAccount->setGivenName("TestAmbrone1");
        $expectedPbnlAccount->setUid("testambrone1");
        $expectedPbnlAccount->setSn("ef");
        $expectedPbnlAccount->setCn("TestAmbrone1");
        $expectedPbnlAccount->setUidNumber("814");
        $expectedPbnlAccount->setMail("TestAmbrone1@pbnl.de");
        $expectedPbnlAccount->setUserPassword("{ssha}VxyzFlqfW+MfaG3DghRX0z++sj4JXkamqKVBUg==");
        $expectedPbnlAccount->setDn("givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de");
        $expectedPbnlAccount->setPostalCode("5698");
        $expectedPbnlAccount->setHomeDirectory("/home/TestAmbrone1");
        $expectedPbnlAccount->setL("0");
        $expectedPbnlAccount->setStreet("0");
        $expectedPbnlAccount->setTelephoneNumber("0");
        $expectedPbnlAccount->setMobile("0");

        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("uidNumber","814");

        $this->assertEquals($expectedPbnlAccount, $account);
    }

    public function testReturnOneByFindOneByCall()
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("street","0");

        $this->assertTrue(!is_array($account));
    }

    public function testReturnArrayByFindByCall()
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findBy("street","0");

        $this->assertTrue(is_array($account));
    }

    public function testFindNothing()
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findBy("street","qwdfjbqwdzi delhq dzhqdihq ");

        $this->assertTrue(is_array($account));
        $this->assertEquals(0, count($account));
    }

    public function testFindOneNothing()
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("street","qwdfjbqwdzi delhq dzhqdihq ");

        $this->assertEquals([], $account);
    }

    public function testWrongAttribute()
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("Error","I dont care");

        $this->assertEquals([], $account);
    }
}