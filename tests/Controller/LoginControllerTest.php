<?php

namespace App\Tests\Controller;

use App\Tests\Utils\TestTools;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLogout()
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
        $this->assertStringNotContainsString('Login', $client->getResponse()->getContent());
        $this->assertStringContainsString('Logout', $client->getResponse()->getContent());
        $this->assertStringContainsString('Dashboard', $client->getResponse()->getContent());
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
        $this->assertStringContainsString('Fehlerhafte Zugangsdaten', $client->getResponse()->getContent());


    }

    public function testRedirectToDashboardIfLoggedIn()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/login');
        $client->followRedirect();
        $respons = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringNotContainsString('Login', $respons);
        $this->assertStringContainsString('Logout', $respons);
        $this->assertStringContainsString('Dashboard', $respons);
        //TODO: Change if we have a real start page
    }
}
