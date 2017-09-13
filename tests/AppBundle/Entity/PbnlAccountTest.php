<?php

namespace Tests\AppBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Model\Entity\LDAP\PbnlAccount;

class PbnlAccountTest extends WebTestCase
{
    public function testSetOuWithDn()
    {
        $pbnlAccount = new PbnlAccount();

        $pbnlAccount->setDn("givenName=test123,ou=test1234,ou=people,dc=pbnl,dc=de");
        $this->assertEquals("test1234",$pbnlAccount->getOu());

        $pbnlAccount->setDn("givenName=test123,ou=2345#ä5667,ou=people,dc=pbnl,dc=de");
        $this->assertEquals("2345#ä5667",$pbnlAccount->getOu());

        $pbnlAccount->setDn("givenName=test123,ou=/93fnPwd,ou=people,dc=pbnl,dc=de");
        $this->assertEquals("/93fnPwd",$pbnlAccount->getOu());

    }
}
