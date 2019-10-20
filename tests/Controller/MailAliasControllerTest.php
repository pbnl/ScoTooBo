<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Utils\TestTools;

class MailAliasControllerTest extends WebTestCase
{
    public function testShowDetailGroup()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/mailAlias/detail?mailAlias=ambronen%40pbnl.de');
        $this->assertStringContainsString('TestAmbrone1@pbnl.de', $client->getResponse()->getContent());
        $this->assertStringContainsString('NonExistingUser@pbnl.de', $client->getResponse()->getContent());
        $this->assertStringContainsString('TestBuvoUser@pbnl.de', $client->getResponse()->getContent());
    }

    public function testShowAllGroupsTestAmbrone()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request("GET","/mailAlias/show/all");

        $this->assertEquals("200", $client->getResponse()->getStatusCode());

        $this->assertStringContainsString("webmaster@ambronen.de", $client->getResponse()->getContent());
        $this->assertStringContainsString("wiki@pbnl.de", $client->getResponse()->getContent());
        $this->assertStringContainsString("groupWithMailingList@pbnl.de", $client->getResponse()->getContent());
    }

    public function testAddUserInNordlichtGroup()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/mailAlias/detail?mailAlias=nordlichter%40pbnl.de');

        $form = $crawler->selectButton('Speichern')->form();

        $form['form[forward][1]'] = 'test@test.de';

        $client->submit($form);

        $this->assertEquals("200", $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/mailAlias/detail?mailAlias=nordlichter%40pbnl.de');
        $respons = $client->getResponse()->getContent();

        $this->assertStringContainsString('test@test.de', $respons);
    }

    public function testAddAndRemoveMailAlias()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/mailAlias/show/all');

        $form = $crawler->selectButton('Hinzufügen')->form();

        $form['form[mail]'] = 'testAddMaiLAlias@pbnl.de';

        $client->submit($form);

        $this->assertEquals("200", $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/mailAlias/show/all');
        $respons = $client->getResponse()->getContent();

        $this->assertStringContainsString('testAddMaiLAlias@pbnl.de', $respons);

        $crawler = $client->request("GET","/mailAlias/detail?mailAlias=testAddMaiLAlias%40pbnl.de");

        $this->assertEquals("200", $client->getResponse()->getStatusCode());

        $this->assertStringContainsString("TestAmbrone1@pbnl.de", $client->getResponse()->getContent());

        $form = $crawler->selectButton('Speichern')->form();

        $value =  $form->getPhpValues();
        $value["form"]["forward"] = array();

        $crawler = $client->request($form->getMethod(), $form->getUri(), $value,
            $form->getPhpFiles());

        $crawler = $client->request('GET', '/mailAlias/show/all');
        $respons = $client->getResponse()->getContent();

        $this->assertStringNotContainsString('testAddMaiLAlias@pbnl.de', $respons);
    }
}
