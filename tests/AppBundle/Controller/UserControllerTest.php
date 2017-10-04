<?php

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestTools;

class UserControllerTest extends WebTestCase
{

    public function testShowAllUsers()
    {
        TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/show/all');

        $this->assertContains('givenName=TestBuvoUser,ou=Ambronen,ou=People,dc=pbnl,dc=de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('givenName=TestTronjer,ou=Hagen von Tronje,ou=People,dc=pbnl,dc=de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());

    }

    public function testShowAllUsersSearchName()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByUid';
        $form['form[filterText]'] = '1';

        TestTools::getLoggedInStavoAmbrone()->submit($form);

        $this->assertContains('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testShowAllUsersSearchGroup()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByGroup';
        $form['form[filterText]'] = 'ambronen';

        TestTools::getLoggedInStavoAmbrone()->submit($form);

        $this->assertContains('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('givenName=TestBuvoUser,ou=Ambronen,ou=People,dc=pbnl,dc=de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testShowAllUsersGroupNotFound()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/show/all');

        $form = $crawler->selectButton('Suchen')->form();

        $form['form[filterOption]'] = 'filterByGroup';
        $form['form[filterText]'] = 'WEgregg';

        TestTools::getLoggedInStavoAmbrone()->submit($form);

        $this->assertContains('We cant find the group WEgregg', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testAddUser()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[firstName]'] = 'firstName123';
        $form['form[lastName]'] = 'lastName123';
        $form['form[givenName]'] = 'givenName123';
        $form['form[clearPassword]'] = 'password123';
        $form['form[stamm]'] = 'Ambronen';

        TestTools::getLoggedInStavoAmbrone()->submit($form);
        $respons = TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent();

        $this->assertContains('Benutzer givenname123 hinzugefügt', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testAddUserUserAlreadyExistException()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[firstName]'] = 'firstName123';
        $form['form[lastName]'] = 'lastName123';
        $form['form[givenName]'] = 'TestAmbrone1';
        $form['form[clearPassword]'] = 'password123';
        $form['form[stamm]'] = 'Ambronen';

        TestTools::getLoggedInStavoAmbrone()->submit($form);
        $respons = TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent();

        $this->assertContains('The user testambrone1 already exists.', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testAddUserUserWrongStamm()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[firstName]'] = 'firstName123';
        $form['form[lastName]'] = 'lastName123';
        $form['form[givenName]'] = 'testName55';
        $form['form[clearPassword]'] = 'password123';
        $form->get('form[stamm]')->disableValidation()->setValue("wrong");

        TestTools::getLoggedInStavoAmbrone()->submit($form);

        $this->assertNotContains('testName55 hinzugefügt', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testgetUserDetailsOfOwnUser()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/detail');

        $this->assertContains('givenName=TestAmbrone1,ou=Ambronen,ou=People,dc=pbnl,dc=de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testgetUserDetailsOfOtherUser()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/detail?uid=testambrone2');

        $this->assertContains('givenName=TestAmbrone2,ou=Ambronen,ou=People,dc=pbnl,dc=de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testgetUserDetailsOfOwenUserAndEdit()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/detail');

        $form = $crawler->selectButton('Speichern')->form();

        $form['form[firstName]'] = 'testFirstNameA';
        $form['form[lastName]'] = 'testLastNameB';
        $form['form[postalCode]'] = '89345';
        $form['form[city]'] = 'testCityD';
        $form['form[street]'] = 'testStreetE';
        $form['form[mobilePhoneNumber]'] = 'testMobileF';
        $form['form[homePhoneNumber]'] = 'testPhoneG';

        TestTools::getLoggedInStavoAmbrone()->submit($form);
        $respons = TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent();

        $this->assertContains('Änderungen gespeichert', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());

        $this->assertContains('testFirstNameA', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('testLastNameB', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('89345', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('testCityD', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('testStreetE', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('testMobileF', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('testPhoneG', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testDelUser()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/remove?uid=deletetestambrone');

        TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/show/all');

        $this->assertNotContains('givenName=deleteTestAmbrone,ou=Ambronen,ou=People,dc=pbnl,dc=de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testDelUserUserDoesNotExist()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/remove?uid=notExisting');

        $this->assertNotContains('Der User notExisting existiert nicht!', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testDelUserUserNotUnique()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/remove?uid=notunique');

        $this->assertNotContains('Der User notunique ist nicht einzigartig!', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }

    public function testDelUserNotAllowedException()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/remove?uid=deletetestambrone');

        TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/show/all');

        $this->assertNotContains('givenName=deleteTestAmbrone,ou=Ambronen,ou=People,dc=pbnl,dc=de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());

        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/remove?uid=testtronjer');

        $this->assertEquals("403",TestTools::getLoggedInStavoAmbrone()->getResponse()->getStatusCode());


        TestTools::getLoggedInStavoAmbrone()->request('GET', '/users/show/all');

        $this->assertContains('givenName=TestTronjer,ou=Hagen von Tronje,ou=People,dc=pbnl,dc=de', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
    }
}
