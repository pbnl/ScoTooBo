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

    public function testShowAllUsers()
    {
        $this->loggedInClient->request('GET', '/users/show/all');

        $this->assertContains('givenName=TestBuvoUser,ou=Ambronen,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());
        $this->assertContains('givenName=TestTronjer,ou=Hagen von Tronje,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());

    }

    public function testShowAllUsersSearchName()
    {
        $crawler = $this->loggedInClient->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByUid';
        $form['form[filterText]'] = '1';

        $this->loggedInClient->submit($form);

        $this->assertContains('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());
    }

    public function testShowAllUsersSearchGroup()
    {
        $crawler = $this->loggedInClient->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByGroup';
        $form['form[filterText]'] = 'ambronen';

        $this->loggedInClient->submit($form);

        $this->assertContains('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());
        $this->assertContains('givenName=TestBuvoUser,ou=Ambronen,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());
    }

    public function testShowAllUsersGroupNotFound()
    {
        $crawler = $this->loggedInClient->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByGroup';
        $form['form[filterText]'] = 'WEgregg';

        $this->loggedInClient->submit($form);

        $this->assertContains('We cant find the group WEgregg', $this->loggedInClient->getResponse()->getContent());
    }

    public function testAddUser()
    {
        $crawler = $this->loggedInClient->request('GET', '/users/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[firstName]'] = 'firstName123';
        $form['form[lastName]'] = 'lastName123';
        $form['form[givenName]'] = 'givenName123';
        $form['form[clearPassword]'] = 'password123';
        $form['form[stamm]'] = 'Ambronen';

        $this->loggedInClient->submit($form);
        $respons = $this->loggedInClient->getResponse()->getContent();

        $this->assertContains('Benutzer givenname123 hinzugefügt', $this->loggedInClient->getResponse()->getContent());
    }

    public function testAddUserUserAlreadyExistException()
    {
        $crawler = $this->loggedInClient->request('GET', '/users/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[firstName]'] = 'firstName123';
        $form['form[lastName]'] = 'lastName123';
        $form['form[givenName]'] = 'TestAmbrone1';
        $form['form[clearPassword]'] = 'password123';
        $form['form[stamm]'] = 'Ambronen';

        $this->loggedInClient->submit($form);
        $respons = $this->loggedInClient->getResponse()->getContent();

        $this->assertContains('The user testambrone1 already exists.', $this->loggedInClient->getResponse()->getContent());
    }

    public function testgetUserDetailsOfOwnUser()
    {
        $crawler = $this->loggedInClient->request('GET', '/users/detail');

        $this->assertContains('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());
    }

    public function testgetUserDetailsOfOtherUser()
    {
        $crawler = $this->loggedInClient->request('GET', '/users/detail?uid=testambrone2');

        $this->assertContains('givenName=TestAmbrone2,ou=Ambronen,ou=People,dc=pbnl,dc=de', $this->loggedInClient->getResponse()->getContent());
    }

    public function testgetUserDetailsOfOwenUserAndEdit()
    {
        $crawler = $this->loggedInClient->request('GET', '/users/detail');

        $form = $crawler->selectButton('Speichern')->form();

        $form['form[firstName]'] = 'testFirstNameA';
        $form['form[lastName]'] = 'testLastNameB';
        $form['form[postalCode]'] = '89345';
        $form['form[city]'] = 'testCityD';
        $form['form[street]'] = 'testStreetE';
        $form['form[mobilePhoneNumber]'] = 'testMobileF';
        $form['form[homePhoneNumber]'] = 'testPhoneG';

        $this->loggedInClient->submit($form);
        $respons = $this->loggedInClient->getResponse()->getContent();

        $this->assertContains('Änderungen gespeichert', $this->loggedInClient->getResponse()->getContent());

        $this->assertContains('testFirstNameA', $this->loggedInClient->getResponse()->getContent());
        $this->assertContains('testLastNameB', $this->loggedInClient->getResponse()->getContent());
        $this->assertContains('89345', $this->loggedInClient->getResponse()->getContent());
        $this->assertContains('testCityD', $this->loggedInClient->getResponse()->getContent());
        $this->assertContains('testStreetE', $this->loggedInClient->getResponse()->getContent());
        $this->assertContains('testMobileF', $this->loggedInClient->getResponse()->getContent());
        $this->assertContains('testPhoneG', $this->loggedInClient->getResponse()->getContent());
    }
}