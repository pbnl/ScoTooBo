<?php

namespace Tests\AppBundle\LdapComponent;

use AppBundle\Model\LdapComponent\LdapConnection;
use AppBundle\Model\LdapComponent\LdapEntryHandler\PbnlAccountLdapHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use AppBundle\Entity\LDAP\PbnlAccount;

class PbnlAccountLdapHandlerTest extends TestCase
{
    public function testRetrievePbnlAccount()
    {
        $filter = "(objectClass=pbnlAccount)";
        $expectedPbnlAccount = new PbnlAccount();
        $expectedPbnlAccount->setGivenName("TestAmbrone1");
        $expectedPbnlAccount->setUid("testambrone1");
        $expectedPbnlAccount->setSn("ef");
        $expectedPbnlAccount->setCn("TestAmbrone1");
        $expectedPbnlAccount->setUidNumber("814");
        $expectedPbnlAccount->setMail("TestAmbrone1@pbnl.de");
        $expectedPbnlAccount->setUserPassword("e3NzaGF9Vnh5ekZscWZXK01mYUczRGdoUlgweisrc2o0SlhrYW1xS1ZCVWc9PQ==");
        $expectedPbnlAccount->setDn("givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de");
        $expectedPbnlAccount->setPostalCode("5698");
        $expectedPbnlAccount->setHomeDirectory("/home/TestAmbrone1");
        $expectedPbnlAccount->setL("0");
        $expectedPbnlAccount->setStreet("0");
        $expectedPbnlAccount->setTelephoneNumber("0");
        $expectedPbnlAccount->setMobile("0");
        $pbnlAccountLdapArray = [[
            "dn" => "givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de",
            "objectclass" => ["pbnlAccount", "posixAccount", "inetOrgPerson"],
            "cn" => ["TestAmbrone1"],
            "gidnumber" => ["501"],
            "homedirectory" => ["/home/TestAmbrone1"],
            "sn" => ["ef"],
            "uid" => ["testambrone1"],
            "uidnumber" => ["814"],
            "givenname" => ["TestAmbrone1"],
            "l" => ["0"],
            "mail" => ["TestAmbrone1@pbnl.de"],
            "mobile" => ["0"],
            "postalcode" => ["5698"],
            "street" => ["0"],
            "telephonenumber" => ["0"],
            "userpassword" => ["e3NzaGF9Vnh5ekZscWZXK01mYUczRGdoUlgweisrc2o0SlhrYW1xS1ZCVWc9PQ=="]],
            "count" => 1
        ];



        $baseDn = "dc=test,dc=de";

        $ldapConnection = $this->getMockBuilder(LdapConnection::class)
            ->setConstructorArgs(["127.0.0.1", "389",true,"",$baseDn, new Logger("")])
            ->getMock();
        $ldapConnection->expects($this->once())
            ->method("ldap_search")
            ->with($baseDn, $filter)
            ->willReturn("search Result");
        $ldapConnection->expects($this->once())
            ->method("ldap_get_entries")
            ->with("search Result")
            ->willReturn($pbnlAccountLdapArray);


        $pbnlAccountHandler = new PbnlAccountLdapHandler($baseDn);
        //TODO: PbnlAccount nicht hard rein schreiben
        $actualPbnlAccount = $pbnlAccountHandler->retrieve("pbnlAccount", $ldapConnection)[0];

        $this->assertEquals($expectedPbnlAccount, $actualPbnlAccount);
    }

    public function testWrongObjectClass()
    {
        $this->expectException(\InvalidArgumentException::class);

        $filter = "(objectClass=pbnlAccount)";
        $pbnlAccountLdapArray = [[
            "dn" => "givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de",
            "objectclass" => ["testAccount", "posixAccount", "inetOrgPerson"],
            "cn" => ["TestAmbrone1"],
            "gidnumber" => ["501"],
            "homedirectory" => ["/home/TestAmbrone1"],
            "sn" => ["ef"],
            "uid" => ["testambrone1"],
            "uidnumber" => ["814"],
            "givenname" => ["TestAmbrone1"],
            "l" => ["0"],
            "mail" => ["TestAmbrone1@pbnl.de"],
            "mobile" => ["0"],
            "postalcode" => ["5698"],
            "street" => ["0"],
            "telephonenumber" => ["0"],
            "userpassword" => ["e3NzaGF9Vnh5ekZscWZXK01mYUczRGdoUlgweisrc2o0SlhrYW1xS1ZCVWc9PQ=="]],
            "count" => 1
        ];



        $baseDn = "dc=test,dc=de";

        $ldapConnection = $this->getMockBuilder(LdapConnection::class)
            ->setConstructorArgs(["127.0.0.1", "389",true,"",$baseDn, new Logger("")])
            ->getMock();
        $ldapConnection->expects($this->once())
            ->method("ldap_search")
            ->with($baseDn, $filter)
            ->willReturn("search Result");
        $ldapConnection->expects($this->once())
            ->method("ldap_get_entries")
            ->with("search Result")
            ->willReturn($pbnlAccountLdapArray);



        $pbnlAccountHandler = new PbnlAccountLdapHandler($baseDn);
        $actualPbnlAccount = $pbnlAccountHandler->retrieve("pbnlAccount", $ldapConnection)[0];
    }
}