<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 04.09.17
 * Time: 00:25
 */

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class LoginControllerTest extends WebTestCase
{
    public function testLogout()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/logout');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        //TODO: Only asserting 302 is not enough
    }

    public function testCorrectLogin()
    {
        //Correct login
        $client = static::createClient();
        $crawler = $client->request('GET', '/logout');


        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();

        $form['_username'] = 'TestAmbrone1';
        $form['_password'] = 'test';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertContains('Read the documentation to learn', $client->getResponse()->getContent());
        //TODO: Change if we have a real start page
    }

    public function testInCorrectLogin()
    {
        //Uncorrect login
        $client = static::createClient();
        $crawler = $client->request('GET', '/logout');

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();

        $form['_username'] = 'TestAmbrone1';
        $form['_password'] = 'hans';

        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertContains('Invalid credentials.', $client->getResponse()->getContent());


    }
}
