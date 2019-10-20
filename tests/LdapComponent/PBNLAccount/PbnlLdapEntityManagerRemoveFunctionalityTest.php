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
use App\Tests\Other\PbnlNativeAliceLoader;
use App\Tests\Utils\TestTools;
use Doctrine\ORM\EntityNotFoundException;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class PbnlLdapEntityManagerRemoveFunctionalityTest extends TestCase
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

    /**
     * @dataProvider providePbnlAccounts
     */
    public function testRemovePbnlAccount(PbnlAccount $oldPbnlAccount)
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $ldapManager->persist($oldPbnlAccount);
        $ldapManager->flush();

        $account = $pbnlRepo->findOneBy("uid", $oldPbnlAccount->getUid());
        $this->assertEquals($oldPbnlAccount, $account);

        $ldapManager->delete($oldPbnlAccount);
        $ldapManager->flush();

        $account = $pbnlRepo->findOneBy("uid", $oldPbnlAccount->getUid());
        $this->assertEquals([], $account);
    }

    public function providePbnlAccounts()
    {
        $loader = new PbnlNativeAliceLoader(124775);
        $objectSet = $loader->loadFile(__DIR__ . '/PbnlAccounts.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);
        return $dataSet;
    }

    public function testRemoveNotExistingPbnlAccount()
    {
        $this->expectException(EntityNotFoundException::class);

        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setGivenName("aStrangeNeverExistingName2404719");
        $pbnlAccount->setUid("aStrangeNeverExistingName2404719");
        $pbnlAccount->setSn("ef");
        $pbnlAccount->setCn("TestAmbrone1");
        $pbnlAccount->setUidNumber("24632");
        $pbnlAccount->setMail("TestAmbrone1@pbnl.de");
        $pbnlAccount->setUserPassword("{ssha}VxyzFlqfW+MfaG3DghRX0z++sj4JXkamqKVBUg==");
        $pbnlAccount->setPostalCode("5698");
        $pbnlAccount->setHomeDirectory("/home/TestAmbrone1");
        $pbnlAccount->setL("0");
        $pbnlAccount->setStreet("0");
        $pbnlAccount->setTelephoneNumber("0");
        $pbnlAccount->setMobile("0");
        $pbnlAccount->setOu("Ambronen");

        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("uid", $pbnlAccount->getUid());
        $this->assertEquals([], $account);

        $ldapManager->delete($pbnlAccount);
        $ldapManager->flush();
    }
}
