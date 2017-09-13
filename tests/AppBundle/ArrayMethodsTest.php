<?php

namespace Tests\AppBundle;


use AppBundle\ArrayMethods;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArrayMethodsTest extends WebTestCase
{
    public function testValueToKeyAndValue()
    {
        $array = ["a","b","c"];
        $arrays = ["a"=>"a","b"=>"b","c"=>"c"];
        $array = ArrayMethods::valueToKeyAndValue($array);

        $this->assertEquals($arrays, $array);
    }
}