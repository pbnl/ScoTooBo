<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestTools;

class GroupControllerTest extends WebTestCase
{
    public function testShowAllGroupsTestAmbrone()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/groups/show/all');
        $this->assertContains('ambronen', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('webmaster@schulung.pbnl.de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertNotContains('buvo', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testShowAllGroupsTestTronjer()
    {
        $crawler = TestTools::getLoggedInTronjer()->request('GET', '/groups/show/all');
        $this->assertContains('elder', TestTools::getLoggedInTronjer()->getResponse()->getContent());
        $this->assertNotContains('buvo', TestTools::getLoggedInTronjer()->getResponse()->getContent());
    }

    public function testShowAllGroupsTestBuvoUser()
    {
        $crawler = TestTools::getLoggedInBuvoUser()->request('GET', '/groups/show/all');
        $this->assertContains('elder', TestTools::getLoggedInBuvoUser()->getResponse()->getContent());
        $this->assertContains('buvo', TestTools::getLoggedInBuvoUser()->getResponse()->getContent());
        $this->assertContains('schulung', TestTools::getLoggedInBuvoUser()->getResponse()->getContent());
    }

    public function testShowAllGroupsTestGrueppling()
    {
        $crawler = TestTools::getLoggedInTestGrueppling()->request('GET', '/groups/show/all');
        $this->assertEquals("403",TestTools::getLoggedInTestGrueppling()->getResponse()->getStatusCode());
    }


    public function testShowDetailGroup()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request("GET","/groups/detail?groupCn=schulung");

        $this->assertEquals("200",TestTools::getLoggedInStavoAmbrone()->getResponse()->getStatusCode());

        $this->assertContains("TestAmbrone1", TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());

        $crawler = TestTools::getLoggedInStavoAmbrone()->request("GET","/groups/detail?groupCn=groupWithMailingList");

        $this->assertEquals("200",TestTools::getLoggedInStavoAmbrone()->getResponse()->getStatusCode());

        $this->assertContains("TestAmbrone2", TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains("TestBuvoUser", TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains("TestAmbrone1", TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testShowDetailGroupUserDoesNotExist()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request("GET","/groups/detail?groupCn=groupWithMailingList");

        $this->assertEquals("200",TestTools::getLoggedInStavoAmbrone()->getResponse()->getStatusCode());

        $this->assertContains("TestAmbrone2", TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains("TestBuvoUser", TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains("TestAmbrone1", TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains(
            "The user with the dn: givenName=NotExistingUser,ou=Ambronen,ou=People,dc=pbnl,dc=de does not exist!",
            TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }
}
