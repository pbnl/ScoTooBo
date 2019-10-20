<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 15.03.18
 * Time: 21:02
 */

namespace App\Tests\LdapComponent;


use App\Entity\LDAP\PbnlAccount;
use App\Model\LdapComponent\PbnlLdapEntityManager;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class PbnlLdapEntityManagerFindFunctionalityTest extends TestCase
{

    private $ldapConnectionConfig = array();

    public function setUp(): void
    {
        $this->ldapConnectionConfig["uri"] = "127.0.0.1";
        $this->ldapConnectionConfig["use_tls"] = false;
        $this->ldapConnectionConfig["port"] = 389;
        $this->ldapConnectionConfig["password"] = "admin";
        $this->ldapConnectionConfig["bind_dn"] = "cn=admin,dc=pbnl,dc=de";
        $this->ldapConnectionConfig["base_dn"] = "dc=pbnl,dc=de";
    }

    public function testRetriveOnePbnlAccountByGivenName()
    {
        $expectedPbnlAccount = new PbnlAccount();
        $expectedPbnlAccount->setGivenName("TestAmbrone2");
        $expectedPbnlAccount->setUid("testambrone2");
        $expectedPbnlAccount->setSn("ef");
        $expectedPbnlAccount->setCn("TestAmbrone2");
        $expectedPbnlAccount->setUidNumber("813");
        $expectedPbnlAccount->setMail("testAmbrone2FalscheMail@gmx.de");
        $expectedPbnlAccount->setUserPassword("{ssha}tfMavcfF/ByoFvYNNmvYgaUGWrApS1Cajdsgzg==");
        $expectedPbnlAccount->setDn("givenName=TestAmbrone2,ou=Ambronen,ou=People,dc=pbnl,dc=de");
        $expectedPbnlAccount->setPostalCode("252");
        $expectedPbnlAccount->setHomeDirectory("/home/TestAmbrone2");
        $expectedPbnlAccount->setL("0");
        $expectedPbnlAccount->setStreet("0");
        $expectedPbnlAccount->setTelephoneNumber("0");
        $expectedPbnlAccount->setMobile("0");

        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("givenName", "TestAmbrone2");

        $this->assertEquals($expectedPbnlAccount, $account);
    }

    public function testRetiriveOnePbnlAccountByUidNumber()
    {
        $expectedPbnlAccount = new PbnlAccount();
        $expectedPbnlAccount->setGivenName("TestAmbrone2");
        $expectedPbnlAccount->setUid("testambrone2");
        $expectedPbnlAccount->setSn("ef");
        $expectedPbnlAccount->setCn("TestAmbrone2");
        $expectedPbnlAccount->setUidNumber("813");
        $expectedPbnlAccount->setMail("testAmbrone2FalscheMail@gmx.de");
        $expectedPbnlAccount->setUserPassword("{ssha}tfMavcfF/ByoFvYNNmvYgaUGWrApS1Cajdsgzg==");
        $expectedPbnlAccount->setDn("givenName=TestAmbrone2,ou=Ambronen,ou=People,dc=pbnl,dc=de");
        $expectedPbnlAccount->setPostalCode("252");
        $expectedPbnlAccount->setHomeDirectory("/home/TestAmbrone2");
        $expectedPbnlAccount->setL("0");
        $expectedPbnlAccount->setStreet("0");
        $expectedPbnlAccount->setTelephoneNumber("0");
        $expectedPbnlAccount->setMobile("0");

        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("uidNumber", "813");

        $this->assertEquals($expectedPbnlAccount, $account);
    }

    public function testReturnOneByFindOneByCall()
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("street", "0");

        $this->assertTrue(!is_array($account));
    }

    public function testReturnArrayByFindByCall()
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findBy("street", "0");

        $this->assertTrue(is_array($account));
    }

    public function testFindNothing()
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findBy("street", "qwdfjbqwdzi delhq dzhqdihq ");

        $this->assertTrue(is_array($account));
        $this->assertEquals(0, count($account));
    }

    public function testFindOneNothing()
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("street", "qwdfjbqwdzi delhq dzhqdihq ");

        $this->assertEquals([], $account);
    }

    public function testWrongAttribute()
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("Error", "I dont care");

        $this->assertEquals([], $account);
    }
}