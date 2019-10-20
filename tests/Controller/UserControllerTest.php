<?php

namespace App\Tests\Controller;

use App\Tests\Utils\TestTools;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    public function testShowAllUsers()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $client->request('GET', '/users/show/all');

        $this->assertStringContainsString('givenName=TestBuvoUser,ou=Ambronen,ou=People,dc=pbnl,dc=de', $client->getResponse()->getContent());
        $this->assertStringContainsString('givenName=TestTronjer,ou=Hagen von Tronje,ou=People,dc=pbnl,dc=de', $client->getResponse()->getContent());

    }

    public function testShowAllUsersSearchName()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByUid';
        $form['form[filterText]'] = '1';

        $client->submit($form);

        $this->assertStringContainsString('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', $client->getResponse()->getContent());
    }

    public function testShowAllUsersSearchGroup()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByGroup';
        $form['form[filterText]'] = 'ambronen';

        $client->submit($form);

        $this->assertStringContainsString('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', $client->getResponse()->getContent());
        $this->assertStringContainsString('givenName=TestBuvoUser,ou=Ambronen,ou=People,dc=pbnl,dc=de', $client->getResponse()->getContent());
    }

    public function testShowAllUsersGroupNotFound()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByGroup';
        $form['form[filterText]'] = 'WEgregg';

        $client->submit($form);

        $this->assertStringContainsString('We cant find the group WEgregg', $client->getResponse()->getContent());
    }

    public function testAddUser()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[firstName]'] = 'firstName123';
        $form['form[lastName]'] = 'lastName123';
        $form['form[givenName]'] = 'givenName123';
        $form['form[clearPassword]'] = 'password123';
        $form['form[stamm]'] = 'Ambronen';

        $client->submit($form);
        $respons = $client->getResponse()->getContent();

        $this->assertStringContainsString('Benutzer givenname123 hinzugefügt', $client->getResponse()->getContent());
    }

    public function testAddUserInNordlichtGroup()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[firstName]'] = 'firstName1234';
        $form['form[lastName]'] = 'lastName1234';
        $form['form[givenName]'] = 'givenNameInNordlichtGroup';
        $form['form[clearPassword]'] = 'password1234';
        $form['form[stamm]'] = 'Ambronen';

        $client->submit($form);

        $client->request('GET', '/groups/detail?groupCn=nordlichter');
        $respons = $client->getResponse()->getContent();

        $this->assertStringContainsString('givenNameInNordlichtGroup', $respons);
    }

    public function testAddUserUserAlreadyExistException()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[firstName]'] = 'firstName123';
        $form['form[lastName]'] = 'lastName123';
        $form['form[givenName]'] = 'TestAmbrone1';
        $form['form[clearPassword]'] = 'password123';
        $form['form[stamm]'] = 'Ambronen';

        $client->submit($form);
        $respons = $client->getResponse()->getContent();

        $this->assertStringContainsString('The user testambrone1 already exists.', $client->getResponse()->getContent());
    }

    public function testAddUserUserWrongStamm()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[firstName]'] = 'firstName123';
        $form['form[lastName]'] = 'lastName123';
        $form['form[givenName]'] = 'testName55';
        $form['form[clearPassword]'] = 'password123';
        $form->get('form[stamm]')->disableValidation()->setValue("wrong");

        $client->submit($form);

        $this->assertStringNotContainsString('testName55 hinzugefügt', $client->getResponse()->getContent());
    }

    public function testgetUserDetailsOfOwnUser()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/detail');

        $this->assertStringContainsString('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', $client->getResponse()->getContent());
    }

    public function testgetUserDetailsOfOtherUser()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/detail?uid=testambrone2');

        $this->assertStringContainsString('givenName=TestAmbrone2,ou=Ambronen,ou=People,dc=pbnl,dc=de', $client->getResponse()->getContent());
    }

    public function testgetUserDetailsOfOwenUserAndEdit()
    {
        $client = TestTools::getLoggedInStavoAmbrone();

        $crawler = $client->request('GET', '/users/detail');

        $form = $crawler->selectButton('Speichern')->form();

        $form['form[firstName]'] = 'testFirstNameA';
        $form['form[lastName]'] = 'testLastNameB';
        $form['form[postalCode]'] = '89345';
        $form['form[city]'] = 'testCityD';
        $form['form[street]'] = 'testStreetE';
        $form['form[mobilePhoneNumber]'] = 'testMobileF';
        $form['form[homePhoneNumber]'] = 'testPhoneG';

        $client->submit($form);
        $respons = $client->getResponse()->getContent();

        $this->assertStringContainsString('Änderungen gespeichert', $client->getResponse()->getContent());

        $this->assertStringContainsString('testFirstNameA', $client->getResponse()->getContent());
        $this->assertStringContainsString('testLastNameB', $client->getResponse()->getContent());
        $this->assertStringContainsString('89345', $client->getResponse()->getContent());
        $this->assertStringContainsString('testCityD', $client->getResponse()->getContent());
        $this->assertStringContainsString('testStreetE', $client->getResponse()->getContent());
        $this->assertStringContainsString('testMobileF', $client->getResponse()->getContent());
        $this->assertStringContainsString('testPhoneG', $client->getResponse()->getContent());
    }

    public function testDelUser()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/remove?uid=deletetestambrone');

        $client->request('GET', '/users/show/all');

        $this->assertStringNotContainsString('givenName=deleteTestAmbrone,ou=Ambronen,ou=People,dc=pbnl,dc=de', $client->getResponse()->getContent());
    }

    public function testDelUserUserDoesNotExist()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/remove?uid=notExisting');

        $this->assertStringNotContainsString('Der User notExisting existiert nicht!', $client->getResponse()->getContent());
    }

    public function testDelUserUserNotUnique()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/remove?uid=notunique');

        $this->assertStringNotContainsString('Der User notunique ist nicht einzigartig!', $client->getResponse()->getContent());
    }

    public function testDelUserNotAllowedException()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/users/remove?uid=deletetestambrone');

        $client->request('GET', '/users/show/all');

        $this->assertStringNotContainsString('givenName=deleteTestAmbrone,ou=Ambronen,ou=People,dc=pbnl,dc=de', $client->getResponse()->getContent());

        $crawler = $client->request('GET', '/users/remove?uid=testtronjer');

        $this->assertEquals("403", $client->getResponse()->getStatusCode());


        $client->request('GET', '/users/show/all');

        $this->assertStringContainsString('givenName=TestTronjer,ou=Hagen von Tronje,ou=People,dc=pbnl,dc=de', $client->getResponse()->getContent());
    }
}
