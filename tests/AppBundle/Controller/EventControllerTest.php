<?php

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestTools;

class EventControllerTest extends WebTestCase
{
    public function testShowAllEvents()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $client->request('GET', '/events/show/all');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('TestEvent1', $client->getResponse()->getContent());
        $this->assertContains('TestEvent2', $client->getResponse()->getContent());
        $this->assertContains('TestEvent3', $client->getResponse()->getContent());
        $this->assertContains('TestEvent4', $client->getResponse()->getContent());
        $this->assertContains('TestEvent5', $client->getResponse()->getContent());
        $this->assertContains('TestEvent6', $client->getResponse()->getContent());
        $this->assertContains('TestEvent7', $client->getResponse()->getContent());
        $this->assertContains('TestEvent8', $client->getResponse()->getContent());
        $this->assertContains('TestEvent9', $client->getResponse()->getContent());
        $this->assertContains('TestEvent10', $client->getResponse()->getContent());

    }

    public function testAddEvent()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/events/add');

        $form = $crawler->selectButton('Erstellen')->form();

        $form['form[Name]'] = 'Test Name';
        $form['form[Description]'] = 'Test Beschreibung';
        $form['form[PriceInCent]'] = '12345';
        $form['form[DateFrom][date][year]'] = '2018';
        $form['form[DateFrom][date][month]'] = '1';
        $form['form[DateFrom][date][day]'] = '2';
        $form['form[DateFrom][time][hour]'] = '3';
        $form['form[DateFrom][time][minute]'] = '4';
        $form['form[DateTo][date][year]'] = '2020';
        $form['form[DateTo][date][month]'] = '11';
        $form['form[DateTo][date][day]'] = '12';
        $form['form[DateTo][time][hour]'] = '13';
        $form['form[DateTo][time][minute]'] = '14';
        $form['form[Place]'] = 'Test Ort';

        $client->submit($form);
        $client->followRedirect();
        $respons = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Event wurde mit der Id 11 erstellt.', $respons);
        $this->assertContains('Test Name', $respons);
        $this->assertContains('Test Beschreibung', $respons);
        $this->assertContains('123,45€', $respons);
        $this->assertContains('Test Ort', $respons);
    }
}
