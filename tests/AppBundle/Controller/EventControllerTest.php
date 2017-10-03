<?php

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestTools;

class EventControllerTest extends WebTestCase
{
    public function testShowAllEvents()
    {
        TestTools::getLoggedInStavoAmbrone()->request('GET', '/events/show/all');

        $this->assertEquals(200, TestTools::getLoggedInStavoAmbrone()->getResponse()->getStatusCode());
        $this->assertContains('TestEvent1', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('TestEvent2', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('TestEvent3', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('TestEvent4', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('TestEvent5', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('TestEvent6', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('TestEvent7', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('TestEvent8', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('TestEvent9', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());
        $this->assertContains('TestEvent10', TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent());

    }

    public function testAddEvent()
    {
        $crawler = TestTools::getLoggedInStavoAmbrone()->request('GET', '/events/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[Name]'] = 'Test Name';
        $form['form[Description]'] = 'Test Beschreibung';
        $form['form[PriceInCent]'] = '12345';
        $form['form[DateFrom][date][year]'] = '2012';
        $form['form[DateFrom][date][month]'] = '1';
        $form['form[DateFrom][date][day]'] = '2';
        $form['form[DateFrom][time][hour]'] = '3';
        $form['form[DateFrom][time][minute]'] = '4';
        $form['form[DateTo][date][year]'] = '2013';
        $form['form[DateTo][date][month]'] = '11';
        $form['form[DateTo][date][day]'] = '12';
        $form['form[DateTo][time][hour]'] = '13';
        $form['form[DateTo][time][minute]'] = '14';
        $form['form[Place]'] = 'Test Ort';

        TestTools::getLoggedInStavoAmbrone()->submit($form);
        TestTools::getLoggedInStavoAmbrone()->followRedirect();
        $respons = TestTools::getLoggedInStavoAmbrone()->getResponse()->getContent();

        $this->assertEquals(200, TestTools::getLoggedInStavoAmbrone()->getResponse()->getStatusCode());
        $this->assertContains('Event wurde mit der Id 11 erstellt.', $respons);
        $this->assertContains('Test Name', $respons);
        $this->assertContains('Test Beschreibung', $respons);
        $this->assertContains('123,45€', $respons);
        $this->assertContains('Test Ort', $respons);
    }
}
