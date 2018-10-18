<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestTools;

class MailAliasControllerTest extends WebTestCase
{
    public function testShowDetailGroup()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/mailAlias/detail?mailAlias=ambronen%40pbnl.de');
        $this->assertContains('TestAmbrone1@pbnl.de', $client->getResponse()->getContent());
        $this->assertContains('NonExistingUser@pbnl.de', $client->getResponse()->getContent());
        $this->assertContains('TestBuvoUser@pbnl.de', $client->getResponse()->getContent());
    }

    public function testShowAllGroupsTestAmbrone()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request("GET","/mailAlias/show/all");

        $this->assertEquals("200",$client->getResponse()->getStatusCode());

        $this->assertContains("webmaster@ambronen.de", $client->getResponse()->getContent());
        $this->assertContains("wiki@pbnl.de", $client->getResponse()->getContent());
        $this->assertContains("groupWithMailingList@pbnl.de", $client->getResponse()->getContent());
    }
}
