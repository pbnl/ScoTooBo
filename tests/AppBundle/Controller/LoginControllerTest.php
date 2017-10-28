<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestTools;

class LoginControllerTest extends WebTestCase
{
    public function  testLogout()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        //TODO: Only asserting 302 is not enough
    }

    public function testCorrectLogin()
    {
        //Correct login
        $client = static::createClient();
        $client->request('GET', '/logout');


        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();

        $form['_username'] = 'TestAmbrone1';
        $form['_password'] = 'test';

        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotContains('Login', $client->getResponse()->getContent());
        $this->assertContains('Logout', $client->getResponse()->getContent());
        $this->assertContains('Dashboard (entsteht so langsam)', $client->getResponse()->getContent());
        //TODO: Change if we have a real start page
    }

    public function testInCorrectLogin()
    {
        //Uncorrect login
        $client = static::createClient();
        $client->request('GET', '/logout');

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();

        $form['_username'] = 'TestAmbrone1';
        $form['_password'] = 'hans';

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();
        $this->assertContains('Fehlerhafte Zugangsdaten', $client->getResponse()->getContent());


    }

    public function testRedirectToDashboardIfLoggedIn()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/login');
        TestTools::getLoggedInStavoAmbrone()->followRedirect();
        $respons = TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent();

        $this->assertEquals(200, TestTools::getLoggedInStavoAmbrone()->getResponse()->getStatusCode());
        $this->assertNotContains('Login', $respons);
        $this->assertContains('Logout', $respons);
        $this->assertContains('Dashboard (entsteht so langsam)', $respons);
        //TODO: Change if we have a real start page
    }
}
