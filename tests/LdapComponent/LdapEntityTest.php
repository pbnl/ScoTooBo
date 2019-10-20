<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 30.03.18
 * Time: 18:59
 */

namespace App\Tests\LdapComponent;


use App\Entity\LDAP\LdapEntity;
use App\Entity\LDAP\MissingMustAttributeException;
use App\Tests\Other\PbnlNativeAliceLoader;
use App\Tests\Utils\TestTools;
use PHPUnit\Framework\TestCase;

class LdapEntityTest extends TestCase
{

    /**
     * @dataProvider provideLdapPbnlAccount
     *
     * @param LdapEntity $entity
     * @throws MissingMustAttributeException
     */
    public function testCheckMust(LdapEntity $entity)
    {
        $this->assertTrue($entity->checkMust());
    }

    /**
     * @dataProvider provideLdapPbnlAccountEmptyMust
     *
     * @param LdapEntity $entity
     * @throws MissingMustAttributeException
     */
    public function testCheckMustEmpty(LdapEntity $entity)
    {
        $this->expectException(MissingMustAttributeException::class);
        $entity->checkMust();
    }

    public function provideLdapPbnlAccount()
    {
        $loader = new PbnlNativeAliceLoader(3254);
        $objectSet = $loader->loadFile(__DIR__ . '/PBNLAccount/PbnlAccounts.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);
        return $dataSet;
    }

    public function provideLdapPbnlAccountEmptyMust()
    {
        $loader = new PbnlNativeAliceLoader(3254);
        $objectSet = $loader->loadFile(__DIR__ . '/PBNLAccount/PbnlAccounts.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);

        for ($i = 0; $i < count($dataSet); $i++) {
            $mustFields = $dataSet[$i][0]::$mustFields;
            $mustFieldSetter = "set" . $mustFields[$i % count($mustFields)];
            $dataSet[$i][0]->$mustFieldSetter("");
        }
        return $dataSet;
    }
}