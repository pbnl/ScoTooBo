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
        $form['form[DateFrom][date][year]'] = '2015';
        $form['form[DateFrom][date][month]'] = '1';
        $form['form[DateFrom][date][day]'] = '2';
        $form['form[DateFrom][time][hour]'] = '3';
        $form['form[DateFrom][time][minute]'] = '4';
        $form['form[DateTo][date][year]'] = '2018';
        $form['form[DateTo][date][month]'] = '11';
        $form['form[DateTo][date][day]'] = '12';
        $form['form[DateTo][time][hour]'] = '13';
        $form['form[DateTo][time][minute]'] = '14';
        $form['form[Place]'] = 'Test Ort';

        $client->submit($form);
        $client->followRedirect();
        $respons = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Event wurde mit der Id', $respons);
        $this->assertContains('Test Name', $respons);
        $this->assertContains('Test Beschreibung', $respons);
        $this->assertContains('123,45€', $respons);
        $this->assertContains('Test Ort', $respons);
    }

    public function testGenerateInvitationLink_GenerateLinkForEvent2()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/events/show/all');

        $this->assertEquals(
            1,
            $crawler->filter(
                '#form_2:contains("Das Event ist bereits abgelaufen, wollen Sie wirklich noch einen Link erstellen?")'
            )->count()
        );
        $form = $crawler->filter('#form_2')->form();
        $client->submit($form);
        $client->followRedirect();
        $respons = $client->getResponse()->getContent();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Der Einladungslink für TestEvent2 wurde erzeugt.', $respons);
        $this->assertNotContains('class="alert alert-warning"', $respons);
        $this->assertNotContains('class="alert alert-danger"', $respons);
    }
    public function testGenerateInvitationLink_UpdateLinkForEvent1()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/events/show/all');

        $this->assertEquals(
            0,
            $crawler->filter(
                '#form_1:contains("Das Event ist bereits abgelaufen, wollen Sie wirklich noch einen Link erstellen?")'
            )->count()
        );
        $form = $crawler->filter('#form_1')->form();
        $form['InvitationDateFrom']='2018-04-29 78:28:34';
        $form['InvitationDateTo']='2200-01-01 00:00:61';
        $client->submit($form);
        $client->followRedirect();
        $respons = $client->getResponse()->getContent();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Der neue Startzeitunkt wurde nicht verstanden und bleibt unverändert bei: 2018-04-29 08:28:34', $respons);
        $this->assertContains('Der neue Endzeitunkt wurde nicht verstanden und bleibt unverändert bei: 2200-01-01 00:00:00', $respons);
        $this->assertContains('Der Einladungslink für TestEvent1 wurde geändert.', $respons);
        $this->assertNotContains('class="alert alert-danger"', $respons);
    }
    public function testGenerateInvitationLink_GenerateLinkForEvent3()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/events/show/all');

        $this->assertEquals(
            1,
            $crawler->filter(
                '#form_3:contains("Das Event ist bereits abgelaufen, wollen Sie wirklich noch einen Link erstellen?")'
            )->count()
        );
        $form = $crawler->filter('#form_3')->form();
        $form['InvitationDateFrom']='2018-04-29 78:28:34';
        $form['InvitationDateTo']='2200-01-01 00:00:61';
        $client->submit($form);
        $client->followRedirect();
        $respons = $client->getResponse()->getContent();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Der neue Startzeitunkt wurde nicht verstanden und wurde auf jetzt gesetzt: ', $respons);
        $this->assertContains('Der neue Endzeitunkt wurde nicht verstanden und wurde auf den Start des Events gesetzt: 2011-01-01 00:00:00', $respons);
        $this->assertContains('Der Einladungslink für TestEvent3 wurde erzeugt.', $respons);
        $this->assertNotContains('class="alert alert-danger"', $respons);
    }

    public function testShowParticipantsList()
    {
        $client = TestTools::getLoggedInStavoAmbrone();
        $crawler = $client->request('GET', '/events/show/participants/5');
        $respons = $client->getResponse()->getContent();

        $this->assertEquals(
            1,
            $crawler->filter(
                'table'
            )->count()
        );
        $this->assertEquals(
            4,
            $crawler->filter(
                'table tr'
            )->count()
        ); // Kopfzeile, 2 Einträge, Fußzeile

        $this->assertContains('1', $respons);
        $this->assertContains('09.10.2017 21:25:14', $respons);
        $this->assertContains('testadmin testadmin', $respons);
        $this->assertContains('123 45a,', $respons);
        $this->assertContains('12345 Hamburg', $respons);
        $this->assertContains('Ambronen', $respons);
        $this->assertContains('456', $respons);
        $this->assertContains('keine Angabe', $respons);
        $this->assertContains('789', $respons);
        $this->assertContains('2', $respons);
        $this->assertContains('19.10.2017 21:57:50', $respons);
        $this->assertContains('testAdmin testAdmin', $respons);
    }
}
