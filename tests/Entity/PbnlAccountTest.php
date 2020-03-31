<?php

namespace App\Tests\Entity;

use App\Entity\LDAP\PbnlAccount;
use BadMethodCallException;
use PHPUnit\Framework\TestCase;

class PbnlAccountTest extends TestCase
{
    public function testSetOuWithDn()
    {
        $pbnlAccount = new PbnlAccount();

        $pbnlAccount->setDn("givenName=test123,ou=test1234,ou=people,dc=pbnl,dc=de");
        $this->assertEquals("test1234", $pbnlAccount->getOu());

        $pbnlAccount->setDn("givenName=test123,ou=2345#5667,ou=people,dc=pbnl,dc=de");
        $this->assertEquals("2345#5667", $pbnlAccount->getOu());

        $pbnlAccount->setDn("givenName=test123,ou=/93fnPwd,ou=people,dc=pbnl,dc=de");
        $this->assertEquals("/93fnPwd", $pbnlAccount->getOu());

    }

    public function testGenerateDNErrors()
    {
        $pbnlAccount = new PbnlAccount();
        $this->expectException(BadMethodCallException::class);
        $pbnlAccount->getDn();

        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setGivenName("test");
        $this->expectException(BadMethodCallException::class);
        $pbnlAccount->getDn();

        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setOu("ee");
        $this->expectException(BadMethodCallException::class);
        $pbnlAccount->getDn();
    }

    public function testGenerateDN()
    {
        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setGivenName("test123");
        $pbnlAccount->setOu("ee");
        $this->assertEquals("givenName=test123,ou=ee,ou=People,dc=pbnl,dc=de", $pbnlAccount->getDn());

        $pbnlAccount = new PbnlAccount();
        $pbnlAccount->setDn("givenName=test123,ou=2345#ä5667,ou=people,dc=pbnl,dc=de");
        $pbnlAccount->setGivenName("test123");
        $pbnlAccount->setOu("ee");
        $this->assertEquals("givenName=test123,ou=2345#ä5667,ou=people,dc=pbnl,dc=de", $pbnlAccount->getDn());
    }

    public function testErrorDn()
    {
        $this->expectException(BadMethodCallException::class);

        $newPbnlAccount = new PbnlAccount();
        $newPbnlAccount->setGivenName("TestAmbrone1");
        $newPbnlAccount->setUid("testambrone1");
        $newPbnlAccount->setSn("TestAmbrone1");
        $newPbnlAccount->setCn("tewstcn");
        $newPbnlAccount->setUidNumber("815");
        $newPbnlAccount->setDn("Error");

    }
}
