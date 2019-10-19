<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Utils\TestTools;

class GroupControllerTest extends WebTestCase
{
    public function testShowAllGroupsTestAmbrone()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/groups/show/all');
        $this->assertContains('ambronen', $client->getResponse()->getContent());
        $this->assertContains('webmaster@schulung.pbnl.de', $client->getResponse()->getContent());
        $this->assertNotContains('buvo', $client->getResponse()->getContent());
    }

    public function testShowAllGroupsTestTronjer()
    {
        $client = TestTools::getLoggedInTronjer();
        $crawler = $client->request('GET', '/groups/show/all');
        $this->assertContains('elder', $client->getResponse()->getContent());
        $this->assertNotContains('buvo', $client->getResponse()->getContent());
    }

    public function testShowAllGroupsTestBuvoUser()
    {
        $client = TestTools::getLoggedInBuvoUser();
        $crawler = $client->request('GET', '/groups/show/all');
        $this->assertContains('elder', $client->getResponse()->getContent());
        $this->assertContains('buvo', $client->getResponse()->getContent());
        $this->assertContains('schulung', $client->getResponse()->getContent());
    }

    public function testShowAllGroupsTestGrueppling()
    {
        $client = TestTools::getLoggedInTestGrueppling();
        $crawler = $client->request('GET', '/groups/show/all');
        $this->assertEquals("403",$client->getResponse()->getStatusCode());
    }


    public function testShowDetailGroup()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request("GET","/groups/detail?groupCn=schulung");

        $this->assertEquals("200",$client->getResponse()->getStatusCode());

        $this->assertContains("TestAmbrone1", $client->getResponse()->getContent());

        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request("GET","/groups/detail?groupCn=groupWithMailingList");

        var_dump($client->getResponse()->getContent());

        $this->assertEquals("200",$client->getResponse()->getStatusCode());

        $this->assertContains("TestAmbrone2", $client->getResponse()->getContent());
        $this->assertContains("TestBuvoUser", $client->getResponse()->getContent());
        $this->assertContains("TestAmbrone1", $client->getResponse()->getContent());
    }

    public function testShowDetailGroupUserDoesNotExist()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request("GET","/groups/detail?groupCn=groupWithMailingList");

        $this->assertEquals("200",$client->getResponse()->getStatusCode());

        $this->assertContains("TestAmbrone2", $client->getResponse()->getContent());
        $this->assertContains("TestBuvoUser", $client->getResponse()->getContent());
        $this->assertContains("TestAmbrone1", $client->getResponse()->getContent());
        $this->assertContains(
            "The user with the dn: givenName=NotExistingUser,ou=Ambronen,ou=People,dc=pbnl,dc=de does not exist!",
            $client->getResponse()->getContent());
    }
}
