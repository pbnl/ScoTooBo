<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 15.03.18
 * Time: 21:02
 */

namespace Tests\AppBundle\LdapComponent;


use AppBundle\Entity\LDAP\MissingMustAttributeException;
use AppBundle\Entity\LDAP\PbnlAccount;
use AppBundle\Model\LdapComponent\LdapEntryHandler\LdapPersistException;
use AppBundle\Model\LdapComponent\PbnlLdapEntityManager;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Tests\AppBundle\PbnlNativeAliceLoader;
use Tests\AppBundle\TestTools;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Must;

class PbnlLdapEntityManagerUpdateFunctionalityTest extends TestCase
{
    private $ldapConnectionConfig = array();

    public function setUp()
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
    public function testUpdatePbnlAccount(PbnlAccount $oldPbnlAccount)
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $ldapManager->persist($oldPbnlAccount);
        $ldapManager->flush();

        $account = $pbnlRepo->findOneBy("uid",$oldPbnlAccount->getUid());
        $this->assertEquals($oldPbnlAccount, $account);

        $updatedPbnlAccount = $this->updatePbnlAccountWithNewData($oldPbnlAccount);

        $ldapManager->persist($updatedPbnlAccount);
        $ldapManager->flush();

        $account = $pbnlRepo->findOneBy("uid",$oldPbnlAccount->getUid());
        $this->assertEquals($updatedPbnlAccount, $account);
    }

    /**
     * @dataProvider providePbnlAccounts
     */
    public function testUpdateWithoutMustPbnlAccount(PbnlAccount $oldPbnlAccount)
    {
        $this->expectException(MissingMustAttributeException::class);

        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlAccount::class);

        $ldapManager->persist($oldPbnlAccount);
        $ldapManager->flush();

        $updatedPbnlAccount = $this->removeMustFields($oldPbnlAccount);

        $ldapManager->persist($updatedPbnlAccount);
        $ldapManager->flush();
    }

    public function providePbnlAccounts()
    {
        $loader = new PbnlNativeAliceLoader(124775);
        $objectSet = $loader->loadFile(__DIR__.'/PbnlAccounts.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);
        return $dataSet;
    }

    public function updatePbnlAccountWithNewData(PbnlAccount $pbnlAccount)
    {
        $pbnlAccount->setCn("3235425");
        $pbnlAccount->setGidNumber(34);
        $pbnlAccount->setL("efwf");
        $pbnlAccount->setHomeDirectory("efweg");
        $pbnlAccount->setMail("efwg");
        $pbnlAccount->setMobile("efwrgqg22 ");
        $pbnlAccount->setPostalCode("433");
        $pbnlAccount->setSn("eew");
        $pbnlAccount->setStreet("WERGff");
        $pbnlAccount->setTelephoneNumber("efegg");

        return $pbnlAccount;
    }

    private function removeMustFields($oldPbnlAccount)
    {
        $i = random_int(0,100);
        $setFunction = "set".PbnlAccount::$mustFields[$i%count(PbnlAccount::$mustFields)];

        $oldPbnlAccount->$setFunction("");

        return $oldPbnlAccount;
    }
}