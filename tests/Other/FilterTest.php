<?php

namespace App\Tests\Other;

use App\Model\Filter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilterTest extends WebTestCase
{
    public function testAddFilter() {
        $filter = new Filter();

        $filter->addFilter("test1", "test2");

        $this->assertEquals("test1", $filter->getFilterAttributes()[0]);
        $this->assertEquals("test2", $filter->getFilterTexts()[0]);
    }
}
