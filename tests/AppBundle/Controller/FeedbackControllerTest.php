<?php

namespace Tests\AppBundle\Controller;


use AppBundle\Model\Services\ReCaptchaService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestTools;

class FeedbackControllerTest extends WebTestCase
{
    /**
     * @Legacy
     */
    public function testCreateFeedbackDatabaseEntry()
    {
        $client = static::createClient();

        $reCaptcha = $this->getMockBuilder(ReCaptchaService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $reCaptcha->expects($this->once())
            ->method("validateReCaptcha")
            ->willReturn(true);

        static::$kernel->setKernelModifier(function($kernel) use ($reCaptcha) {
            $kernel->getContainer()->set('reCaptcha', $reCaptcha);
        });

        $client->request("POST", "/feedback/send", array(
            "data"=>"[{\"Text\":\"asdf\"},
            \"picture\",
            {\"href\":\"http://127.0.0.1:8000/\",\"ancestorOrigins\":{},\"origin\":\"http://127.0.0.1:8000\",\"protocol\":\"http:\",\"host\":\"127.0.0.1:8000\",\"hostname\":\"127.0.0.1\",\"port\":\"8000\",\"pathname\":\"/\",\"search\":\"\",\"hash\":\"\"},
            \"browser\",
            \"htmlText\",1506893323093,
            \"gukvzccukvuk\"]"
        ));

        $this->assertEquals("200", $client->getResponse()->getStatusCode());
    }

    /**
     * @Legacy
     */
    public function testCreateFeedbackDatabaseEntryWithLoggedInUser()
    {
        $reCaptcha = $this->getMockBuilder(ReCaptchaService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $reCaptcha->expects($this->once())
            ->method("validateReCaptcha")
            ->willReturn(true);

        $client = static::createClient();
        $client->request('GET', '/logout');

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();

        $form['_username'] = 'TestAmbrone1';
        $form['_password'] = 'test';

        $client->submit($form);
        $client->followRedirect();

        static::$kernel->setKernelModifier(function($kernel) use ($reCaptcha) {
            $kernel->getContainer()->set('reCaptcha', $reCaptcha);
        });


        $client->request("POST", "/feedback/send", array(
            "data"=>"[{\"Text\":\"asdf\"},
            \"picture\",
            {\"href\":\"http://127.0.0.1:8000/\",\"ancestorOrigins\":{},\"origin\":\"http://127.0.0.1:8000\",\"protocol\":\"http:\",\"host\":\"127.0.0.1:8000\",\"hostname\":\"127.0.0.1\",\"port\":\"8000\",\"pathname\":\"/\",\"search\":\"\",\"hash\":\"\"},
            \"browser\",
            \"htmlText\",1506893323093,
            \"ewfwewgerg\"]"
        ));

        var_dump($client->getResponse()->getContent());

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
            \"efef\",
            ,
            \"ewfwewgerg\"]"
        ));

        $this->assertContains("Object(AppBundle\Entity\UserFeedback).browserData:", $client->getResponse()->getContent());
        $this->assertContains("Dieser Wert sollte nicht leer sein.", $client->getResponse()->getContent());
        $this->assertContains("Object(AppBundle\Entity\UserFeedback).url", $client->getResponse()->getContent());
        $this->assertContains("Object(AppBundle\Entity\UserFeedback).htmlContent", $client->getResponse()->getContent());
        $this->assertContains("Object(AppBundle\Entity\UserFeedback).picture:", $client->getResponse()->getContent());
        $this->assertContains("Object(AppBundle\Entity\UserFeedback).browserData:", $client->getResponse()->getContent());


        $this->assertEquals("500", $client->getResponse()->getStatusCode());
    }
}