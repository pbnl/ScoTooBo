<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 08.09.17
 * Time: 19:37
 */

namespace Tests\AppBundle;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestTools extends WebtestCase
{
    public static function getLoggedInUser() {
        //Correct login
        $client = static::createClient();
        $client->request('GET', '/logout');


        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();

        $form['_username'] = 'TestAmbrone1';
        $form['_password'] = 'test';

        $client->submit($form);
        $client->followRedirect();

        return $client;
    }
}