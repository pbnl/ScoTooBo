<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Model\Entity\LDAP\PbnlAccount;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class PbnlAccountTest extends TestCase
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
