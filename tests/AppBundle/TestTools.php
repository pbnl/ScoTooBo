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
    private static $loggedInStavoAmbrone = null;

    public static function getLoggedInStavoAmbrone() {
        if(TestTools::$loggedInStavoAmbrone == null) {
            //Correct login
            $client = static::createClient();
            $client->request('GET', '/logout');


            $crawler = $client->request('GET', '/login');
            $form = $crawler->selectButton('Login')->form();

            $form['_username'] = 'TestAmbrone1';
            $form['_password'] = 'test';

            $client->submit($form);
            $client->followRedirect();

            TestTools::$loggedInStavoAmbrone = $client;

            return $client;
        }
        else {
            return TestTools::$loggedInStavoAmbrone;
        }
    }
}