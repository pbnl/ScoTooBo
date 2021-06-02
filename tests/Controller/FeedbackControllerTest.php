<?php

namespace App\Tests\Controller;


use App\Tests\Utils\TestTools;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeedbackControllerTest extends WebTestCase
{
    public function testCreateFeedbackDatabaseEntry()
    {
        $client = static::createClient();

        $client->request("POST", "/feedback/send", array(
            "data" => "[{\"Text\":\"asdf\"},
            \"picture\",
            {\"href\":\"http://127.0.0.1:8000/\",\"ancestorOrigins\":{},\"origin\":\"http://127.0.0.1:8000\",\"protocol\":\"http:\",\"host\":\"127.0.0.1:8000\",\"hostname\":\"127.0.0.1\",\"port\":\"8000\",\"pathname\":\"/\",\"search\":\"\",\"hash\":\"\"},
            \"browser\",
            \"htmlText\",1506893323093,
            \"gukvzccukvuk\"]"
        ));
        $this->assertEquals("200", $client->getResponse()->getStatusCode());
    }

    public function testDeleteFeedbackDatabaseEntry()
    {
        $client = TestTools::getLoggedInAdminUser();

        $client->request("POST", "/feedback/send", array(
            "data" => "[{\"Text\":\"TO_DELETE\"},
            \"picture\",
            {\"href\":\"http://127.0.0.1:8000/\",\"ancestorOrigins\":{},\"origin\":\"http://127.0.0.1:8000\",\"protocol\":\"http:\",\"host\":\"127.0.0.1:8000\",\"hostname\":\"127.0.0.1\",\"port\":\"8000\",\"pathname\":\"/\",\"search\":\"\",\"hash\":\"\"},
            \"browser\",
            \"htmlText\",1506893323093,
            \"gukvzccukvuk\"]"
        ));

        $crawler = $client->request('GET', '/feedback/show/all');
        $link = ($crawler->filter('tr:contains("TO_DELETE")'))->selectLink("Löschen")->link()->getUri();
        $client->request("POST", $link);
        $this->assertEquals("302", $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals("200", $client->getResponse()->getStatusCode());
        $this->assertStringContainsString("Feedback wurde gelöscht", $client->getResponse()->getContent());
    }

    public function testCreateFeedbackDatabaseEntryWithLoggedInUser()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();

        $form['_username'] = 'TestAmbrone1';
        $form['_password'] = 'test';

        $client->submit($form);
        $client->followRedirect();

        $client->request("POST", "/feedback/send", array(
            "data" => "[{\"Text\":\"asdf\"},
            \"picture\",
            {\"href\":\"http://127.0.0.1:8000/\",\"ancestorOrigins\":{},\"origin\":\"http://127.0.0.1:8000\",\"protocol\":\"http:\",\"host\":\"127.0.0.1:8000\",\"hostname\":\"127.0.0.1\",\"port\":\"8000\",\"pathname\":\"/\",\"search\":\"\",\"hash\":\"\"},
            \"browser\",
            \"htmlText\",1506893323093,
            \"ewfwewgerg\"]"
        ));
        $this->assertEquals("200", $client->getResponse()->getStatusCode());
    }

    public function testCreateFeedbackDatabaseEntryReCaptchaFail()
    {
        $_ENV["recaptcha_testing_bypass_allow"] = "False";
        $client = static::createClient();
        $client->request('GET', '/logout');

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();

        $form['_username'] = 'TestAmbrone1';
        $form['_password'] = 'test';

        $client->submit($form);
        $client->followRedirect();

        $client->request("POST", "/feedback/send", array(
            "data" => "[{\"Text\":\"asdf\"},
            \"picture\",
            {\"href\":\"http://127.0.0.1:8000/\",\"ancestorOrigins\":{},\"origin\":\"http://127.0.0.1:8000\",\"protocol\":\"http:\",\"host\":\"127.0.0.1:8000\",\"hostname\":\"127.0.0.1\",\"port\":\"8000\",\"pathname\":\"/\",\"search\":\"\",\"hash\":\"\"},
            \"browser\",
            \"htmlText\",1506893323093,
            \"ewfwewgerg\"]"
        ));

        $this->assertEquals("403", $client->getResponse()->getStatusCode());
    }
}
