<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 15.03.18
 * Time: 21:02
 */

namespace App\Tests\LdapComponent;


use App\Entity\LDAP\PbnlMailAlias;
use App\Model\LdapComponent\PbnlLdapEntityManager;
use App\Tests\Other\PbnlNativeAliceLoader;
use App\Tests\Utils\TestTools;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class PbnlMailAliasFunctionalityTest extends TestCase
{
    private $ldapConnectionConfig = array();

    public function setUp(): void
    {
        $this->ldapConnectionConfig["uri"] = "127.0.0.1";
        $this->ldapConnectionConfig["port"] = "389";
        $this->ldapConnectionConfig["use_tls"] = false;
        $this->ldapConnectionConfig["password"] = "admin";
        $this->ldapConnectionConfig["bind_dn"] = "cn=admin,dc=pbnl,dc=de";
        $this->ldapConnectionConfig["base_dn"] = "dc=pbnl,dc=de";
    }

    /**
     * @dataProvider providePbnlMailAliasList
     */
    public function testAddNewPbnlMailAlias(PbnlMailAlias $newPbnlMailAlias)
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlMailAlias::class);

        $pbnlMailAlias = $pbnlRepo->findOneBy("mail", $newPbnlMailAlias->getMail());
        $this->assertEquals([], $pbnlMailAlias);

        $ldapManager->persist($newPbnlMailAlias);
        $ldapManager->flush();

        $pbnlMailAlias = $pbnlRepo->findOneBy("mail", $newPbnlMailAlias->getMail());
        $this->assertEquals($newPbnlMailAlias, $pbnlMailAlias);
    }

    public function providePbnlMailAliasList()
    {
        $loader = new PbnlNativeAliceLoader(85125);
        $objectSet = $loader->loadFile(__DIR__ . '/PbnlMailAliasList.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);
        return $dataSet;
    }

    /**
     * @dataProvider providePbnlMailAliasList
     */
    public function testUpdatePbnlMailAlias(PbnlMailAlias $oldPbnlMailAlias)
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PbnlMailAlias::class);

        $ldapManager->persist($oldPbnlMailAlias);
        $ldapManager->flush();

        $group = $pbnlRepo->findOneBy("mail", $oldPbnlMailAlias->getMail());
        $this->assertEquals($oldPbnlMailAlias, $group);

        $updatedPbnlMailAlias = $this->updatePosixGroupWithNewData($oldPbnlMailAlias);

        $ldapManager->persist($updatedPbnlMailAlias);
        $ldapManager->flush();

        $group = $pbnlRepo->findOneBy("mail", $oldPbnlMailAlias->getMail());
        $this->assertEquals($updatedPbnlMailAlias, $group);
    }

    public function updatePosixGroupWithNewData(PbnlMailAlias $pbnlMailAlias)
    {
        $pbnlMailAlias->setForward(["erghrh2", "rhr4hwe4fq"]);

        return $pbnlMailAlias;
    }
}