<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 08.09.17
 * Time: 19:37
 */

namespace App\Tests\Utils;


use App\Model\User;
use Nelmio\Alice\ObjectSet;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TestTools extends WebtestCase
{
    private static $lastClient = null;

    private static $loggedInStavoAmbrone = null;
    private static $loggedInTronjer = null;
    private static $loggedInBuvoUser = null;
    private static $loggedInTestGrueppling = null;

    public static function getUserFromFile(string $path)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $userAsJson = file_get_contents($path);

        $user = $serializer->deserialize($userAsJson, User::class, 'json');
        $user->generatePasswordAndSalt($user->getPassword());
        return $user;
    }

    public static function getLoggedInStavoAmbrone()
    {
        //Correct login
        $client = static::createClient();
        $session = $client->getContainer()->get('session');

        $firewall = 'main';
        $user = TestTools::getUserFromFile("user-test-data/StavoAmbrone.json");
        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        TestTools::$loggedInStavoAmbrone = $client;
        static::$lastClient = $client;
        return $client;
    }

    public static function getLoggedInTronjer()
    {
        if (TestTools::$loggedInTronjer == null) {
            //Correct login
            $client = static::createClient();
            $session = $client->getContainer()->get('session');

            $firewall = 'main';
            $user = TestTools::getUserFromFile("user-test-data/Tronjer.json");
            $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
            $session->set('_security_' . $firewall, serialize($token));
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());
            $client->getCookieJar()->set($cookie);

            TestTools::$loggedInStavoAmbrone = $client;
            static::$lastClient = $client;
            return $client;
        } else {
            static::$lastClient = TestTools::$loggedInTronjer;
            return TestTools::$loggedInTronjer;
        }
    }

    public static function getLoggedInBuvoUser()
    {
        if (TestTools::$loggedInBuvoUser == null) {
            //Correct login
            $client = static::createClient();
            $session = $client->getContainer()->get('session');

            $firewall = 'main';
            $user = TestTools::getUserFromFile("user-test-data/BuvoUser.json");
            $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
            $session->set('_security_' . $firewall, serialize($token));
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());
            $client->getCookieJar()->set($cookie);

            TestTools::$loggedInStavoAmbrone = $client;
            static::$lastClient = $client;
            return $client;
        } else {
            static::$lastClient = TestTools::$loggedInBuvoUser;
            return TestTools::$loggedInBuvoUser;
        }
    }

    public static function getLoggedInAdminUser()
    {
        if (TestTools::$loggedInBuvoUser == null) {
            //Correct login
            $client = static::createClient();
            $session = $client->getContainer()->get('session');

            $firewall = 'main';
            $user = TestTools::getUserFromFile("user-test-data/AdminUser.json");
            $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
            $session->set('_security_' . $firewall, serialize($token));
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());
            $client->getCookieJar()->set($cookie);

            TestTools::$loggedInStavoAmbrone = $client;
            static::$lastClient = $client;
            return $client;
        } else {
            static::$lastClient = TestTools::$loggedInBuvoUser;
            return TestTools::$loggedInBuvoUser;
        }
    }

    public static function getLoggedInTestGrueppling()
    {
        if (TestTools::$loggedInTestGrueppling == null) {
            //Correct login
            $client = static::createClient();
            $session = $client->getContainer()->get('session');

            $firewall = 'main';
            $user = TestTools::getUserFromFile("user-test-data/TestGrueppling.json");
            $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());
            $session->set('_security_' . $firewall, serialize($token));
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());
            $client->getCookieJar()->set($cookie);

            TestTools::$loggedInStavoAmbrone = $client;
            static::$lastClient = $client;
            return $client;
        } else {
            static::$lastClient = TestTools::$loggedInTestGrueppling;
            return TestTools::$loggedInTestGrueppling;
        }
    }

    public static function objectSetToDataSet(ObjectSet $set)
    {
        $objects = $set->getObjects();
        $dataSet = array();
        foreach ($objects as $object) {
            array_push($dataSet, [$object]);
        }
        return $dataSet;
    }

    public static function getLastClient()
    {
        return static::$lastClient;
    }
}
