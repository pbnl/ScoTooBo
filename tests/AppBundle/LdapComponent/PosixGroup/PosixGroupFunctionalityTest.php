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
use AppBundle\Entity\LDAP\PosixGroup;
use AppBundle\Model\LdapComponent\LdapEntryHandler\LdapPersistException;
use AppBundle\Model\LdapComponent\PbnlLdapEntityManager;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Tests\AppBundle\PbnlNativeAliceLoader;
use Tests\AppBundle\TestTools;

class PosixGroupFunctionalityTest extends TestCase
{
    private $ldapConnectionConfig = array();

    public function setUp()
    {
        $this->ldapConnectionConfig["uri"] = "127.0.0.1";
        $this->ldapConnectionConfig["port"] = "389";
        $this->ldapConnectionConfig["use_tls"] = false;
        $this->ldapConnectionConfig["password"] = "admin";
        $this->ldapConnectionConfig["bind_dn"] = "cn=admin,dc=pbnl,dc=de";
        $this->ldapConnectionConfig["base_dn"] = "dc=pbnl,dc=de";
    }

    /**
     * @dataProvider providePosixGroup
     */
    public function testAddNewPosixGroup(PosixGroup $newPosixGroup)
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PosixGroup::class);

        $group = $pbnlRepo->findOneBy("cn",$newPosixGroup->getCn());
        $this->assertEquals([], $group);

        $ldapManager->persist($newPosixGroup);
        $ldapManager->flush();

        $group = $pbnlRepo->findOneBy("cn",$newPosixGroup->getCn());
        $this->assertEquals($newPosixGroup, $group);
    }

    public function providePosixGroup()
    {
        $loader = new PbnlNativeAliceLoader(85125);
        $objectSet = $loader->loadFile(__DIR__.'/PosixGroups.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);
        return $dataSet;
    }

    /**
     * @dataProvider providePosixGroup
     */
    public function testUpdatePosixGroup(PosixGroup $oldPosixGroup)
    {
        $ldapManager = new PbnlLdapEntityManager(new Logger("logger"), $this->ldapConnectionConfig);
        $pbnlRepo = $ldapManager->getRepository(PosixGroup::class);

        $ldapManager->persist($oldPosixGroup);
        $ldapManager->flush();

        $group = $pbnlRepo->findOneBy("cn",$oldPosixGroup->getCn());
        $this->assertEquals($oldPosixGroup, $group);

        $updatedPosixGroup = $this->updatePosixGroupWithNewData($oldPosixGroup);

        $ldapManager->persist($updatedPosixGroup);
        $ldapManager->flush();

        $group = $pbnlRepo->findOneBy("cn",$oldPosixGroup->getCn());
        $this->assertEquals($updatedPosixGroup, $group);
    }

    public function updatePosixGroupWithNewData(PosixGroup $posixGroup)
    {
        $posixGroup->setGidNumber(random_int(1000,9999));
        $posixGroup->setDescription("wefzqwd efe we qe ef ef81+9ß138u /)T 082334 ß) - ");
        $posixGroup->setMemberUid(["erghrh2", "rhr4hwe4fq"]);

        return $posixGroup;
    }
}