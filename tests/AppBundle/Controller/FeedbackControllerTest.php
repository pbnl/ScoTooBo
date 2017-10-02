<?php

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeedbackControllerTest extends WebTestCase
{
    public function testCreateFeedbackDatabaseEntry()
    {
        $client = static::createClient();
        $client->request("POST", "/feedback/send", array(
            "data"=>"[{\"Text\":\"asdf\"},
            \"picture\",
            {\"href\":\"http://127.0.0.1:8000/\",\"ancestorOrigins\":{},\"origin\":\"http://127.0.0.1:8000\",\"protocol\":\"http:\",\"host\":\"127.0.0.1:8000\",\"hostname\":\"127.0.0.1\",\"port\":\"8000\",\"pathname\":\"/\",\"search\":\"\",\"hash\":\"\"},
            \"browser\",
            \"htmlText\",1506893323093]"
        ));

        $this->assertEquals("200", $client->getResponse()->getStatusCode());
    }
    public function testCreateFeedbackDatabaseEntryNotValidData500()
    {
        $client = static::createClient();
        $client->request("POST", "/feedback/send", array(
            "data"=>"[{\"Text\":\"asdf\"},
            \"\",
            {\"href\":\"\",\"ancestorOrigins\":{},\"origin\":\"http://127.0.0.1:8000\",\"protocol\":\"http:\",\"host\":\"127.0.0.1:8000\",\"hostname\":\"127.0.0.1\",\"port\":\"8000\",\"pathname\":\"/\",\"search\":\"\",\"hash\":\"\"},
            \"\",
            \"efef\",]"
        ));

        var_dump($client->getResponse()->getContent());

        $this->assertContains("Object(AppBundle\Entity\UserFeedback).browserData:", $client->getResponse()->getContent());
        $this->assertContains("Dieser Wert sollte nicht leer sein.", $client->getResponse()->getContent());
        $this->assertContains("Object(AppBundle\Entity\UserFeedback).url", $client->getResponse()->getContent());
        $this->assertContains("Object(AppBundle\Entity\UserFeedback).htmlContent", $client->getResponse()->getContent());
        $this->assertContains("Object(AppBundle\Entity\UserFeedback).picture:", $client->getResponse()->getContent());
        $this->assertContains("Object(AppBundle\Entity\UserFeedback).browserData:", $client->getResponse()->getContent());


        $this->assertEquals("500", $client->getResponse()->getStatusCode());
    }
}