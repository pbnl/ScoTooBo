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
    private static $loggedInTronjer = null;
    private static $loggedInBuvoUser= null;
    private static $loggedInTestGrueppling = null;

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

    public static function getLoggedInTronjer()
    {
        if(TestTools::$loggedInTronjer == null) {
            //Correct login
            $client = static::createClient();
            $client->request('GET', '/logout');


            $crawler = $client->request('GET', '/login');
            $form = $crawler->selectButton('Login')->form();

            $form['_username'] = 'TestTronjer';
            $form['_password'] = 'test';

            $client->submit($form);
            $client->followRedirect();

            TestTools::$loggedInTronjer = $client;

            return $client;
        }
        else {
            return TestTools::$loggedInTronjer;
        }
    }

    public static function getLoggedInBuvoUser()
    {
        if(TestTools::$loggedInBuvoUser == null) {
            //Correct login
            $client = static::createClient();
            $client->request('GET', '/logout');


            $crawler = $client->request('GET', '/login');
            $form = $crawler->selectButton('Login')->form();

            $form['_username'] = 'TestBuvoUser';
            $form['_password'] = 'test';

            $client->submit($form);
            $client->followRedirect();

            TestTools::$loggedInBuvoUser = $client;

            return $client;
        }
        else {
            return TestTools::$loggedInBuvoUser;
        }
    }

    public static function getLoggedInTestGrueppling()
    {
        if(TestTools::$loggedInTestGrueppling == null) {
            //Correct login
            $client = static::createClient();
            $client->request('GET', '/logout');


            $crawler = $client->request('GET', '/login');
            $form = $crawler->selectButton('Login')->form();

            $form['_username'] = 'testgrueppling';
            $form['_password'] = 'test';

            $client->submit($form);
            $client->followRedirect();

            TestTools::$loggedInTestGrueppling = $client;

            return $client;
        }
        else {
            return TestTools::$loggedInTestGrueppling;
        }
    }
}
