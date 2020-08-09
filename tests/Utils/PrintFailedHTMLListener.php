<?php


namespace App\Tests\Utils;


use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrintFailedHTMLListener implements TestListener
{

    use TestListenerDefaultImplementation;

    public function addFailure($test, $e, $time): void
    {
        if ($test instanceof WebTestCase) {
            /** @var Client $client */
            $client = TestTools::getLastClient();

            var_dump($client->getResponse()->getContent());
        }
    }
}