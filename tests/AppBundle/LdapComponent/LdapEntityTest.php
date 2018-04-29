<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 30.03.18
 * Time: 18:59
 */

namespace Tests\AppBundle\LdapComponent;


use AppBundle\Entity\LDAP\LdapEntity;
use AppBundle\Entity\LDAP\MissingMustAttributeException;
use PHPUnit\Framework\TestCase;
use Tests\AppBundle\PbnlNativeAliceLoader;
use Tests\AppBundle\TestTools;

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
        $objectSet = $loader->loadFile(__DIR__.'/PbnlAccounts.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);
        return $dataSet;
    }

    public function provideLdapPbnlAccountEmptyMust()
    {
        $loader = new PbnlNativeAliceLoader(3254);
        $objectSet = $loader->loadFile(__DIR__.'/PbnlAccounts.yml');

        $dataSet = TestTools::objectSetToDataSet($objectSet);

        for ($i = 0; $i < count ($dataSet); $i++)
        {
            $mustFields = $dataSet[$i][0]::$mustFields;
            $mustFieldSetter = "set".$mustFields[$i % count ($mustFields)];
            $dataSet[$i][0]->$mustFieldSetter("");
        }
        return $dataSet;
    }
}