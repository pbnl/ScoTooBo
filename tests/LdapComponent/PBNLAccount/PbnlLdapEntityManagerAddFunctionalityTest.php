<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 15.03.18
 * Time: 21:02
 */

namespace App\Tests\LdapComponent;


use App\Entity\LDAP\MissingMustAttributeException;
use App\Entity\LDAP\PbnlAccount;
use App\Model\LdapComponent\LdapEntryHandler\LdapPersistException;
use App\Model\LdapComponent\PbnlLdapEntityManager;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use App\Tests\Other\PbnlNativeAliceLoader;
use App\Tests\Utils\TestTools;

class PbnlLdapEntityManagerAddFunctionalityTest extends TestCase
{
    private $ldapConnectionConfig = array();

    public function setUp()
    {
        $this->ldapConnectionConfig["uri"] = "127.0.0.1";
        $this->ldapConnectionConfig["use_tls"] = false;
        $this->ldapConnectionConfig["port"] = "389";
        $this->ldapConnectionConfig["password"] = "admin";
        $this->ldapConnectionConfig["bind_dn"] = "cn=admin,dc=pbnl,dc=de";
        $this->ldapConnectionConfig["base_dn"] = "dc=pbnl,dc=de";
    }

    /**
     * @dataProvider providePbnlAccounts
     */
    public function testAddNewPbnlAccount(PbnlAccount $newPbnlAccount)
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $account = $pbnlRepo->findOneBy("uid",$newPbnlAccount->getUid());
        $this->assertEquals([], $account);

        $ldapManager->persist($newPbnlAccount);
        $ldapManager->flush();

        $account = $pbnlRepo->findOneBy("uid",$newPbnlAccount->getUid());
        $this->assertEquals($newPbnlAccount, $account);
    }

    public function providePbnlAccounts()
    {
        $loader = new PbnlNativeAliceLoader(85125);
        $objectSet = $loader->loadFile(__DIR__ . '/PbnlAccounts.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);
        return $dataSet;
    }


    /**
     * @dataProvider provideOuErrorPbnlAccounts
     */
    public function testErrorOU($newPbnlAccount)
    {
        $this->expectException(LdapPersistException::class);

        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);

        $ldapManager->persist($newPbnlAccount);
        $ldapManager->flush();

    }

    public function provideOuErrorPbnlAccounts()
    {
        $loader = new PbnlNativeAliceLoader(3254251);
        $objectSet = $loader->loadFile(__DIR__ . '/PbnlAccounts.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);
        for($i = 0; $i < count($dataSet); $i++)
        {
            $dataSet[$i][0]->setOu("DoesNotExist");
        }

        return $dataSet;
    }

    /**
     * @dataProvider providePbnlAccountWithoutMustField
     */
    public function testErrorEmptyMustField($newPbnlAccount)
    {
        $this->expectException(MissingMustAttributeException::class);

        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);

        $ldapManager->persist($newPbnlAccount);
        $ldapManager->flush();

    }

    public function providePbnlAccountWithoutMustField()
    {
        $loader =  new PbnlNativeAliceLoader(342345);
        $objectSet = $loader->loadFile(__DIR__ . '/PbnlAccounts.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);
        for($i = 0; $i < count($dataSet); $i++)
        {
            $setFunction = "set".PbnlAccount::$mustFields[$i%count(PbnlAccount::$mustFields)];
            $dataSet[$i][0]->$setFunction("");
        }

        return $dataSet;
    }
}