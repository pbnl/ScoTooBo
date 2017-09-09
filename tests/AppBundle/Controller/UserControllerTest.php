<?php

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestTools;

class UserControllerTest extends WebTestCase
{
    private $loggedInClient;

    public function setUp()
    {
        $this->loggedInClient = TestTools::getLoggedInUser();
    }

    public function testShowAllUsers() {
        $this->loggedInClient->request('GET', '/users/show/all');

        $this->assertContains('givenName=TestBuvoUser,ou=Ambronen,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());
        $this->assertContains('givenName=TestTronjer,ou=Hagen von Tronje,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());

    }

    public function testShowAllUsersSearchName() {
        $crawler = $this->loggedInClient->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByUid';
        $form['form[filterText]'] = '1';

        $this->loggedInClient->submit($form);

        $this->assertContains('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());
    }

    public function testShowAllUsersSearchGroup() {
        $crawler = $this->loggedInClient->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByGroup';
        $form['form[filterText]'] = 'ambronen';

        $this->loggedInClient->submit($form);

        $this->assertContains('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());
        $this->assertContains('givenName=TestBuvoUser,ou=Ambronen,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());
    }

    public function testShowAllUsersGroupNotFound() {
        $crawler = $this->loggedInClient->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByGroup';
        $form['form[filterText]'] = 'WEgregg';

        $this->loggedInClient->submit($form);

        $this->assertContains('We cant find the group WEgregg', $this->loggedInClient->getResponse()->getContent());
    }
}