<?php

namespace App\Tests\Other;

use App\Model\SSHA;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SshaTest extends WebTestCase
{
    public function testPasswordGenWithGivenSalt()
    {
        $password = SSHA::sshaPasswordGenWithGivenSalt("asdf",base64_decode("/C/3Wo+j3HU="));
        $this->assertEquals("{SSHA}0n+Klhc8eYp++u8iGwCzqr+hPN38L/daj6PcdQ==", $password);
    }

    public function testPasswordVerifyCorrect()
    {
        $result = SSHA::sshaPasswordVerify("{SSHA}0n+Klhc8eYp++u8iGwCzqr+hPN38L/daj6PcdQ==","asdf");
        $this->assertEquals(true, $result);
    }

    public function testPasswordVerifyIncorrect()
    {
        $result = SSHA::sshaPasswordVerify("{SSHA}OgxZQwV9W1M8grZNX7z/STqjsTz0p+/RXOvKOA==","testo");
        $this->assertEquals(false, $result);
    }

    public function testPasswordGen()
    {
        $password = SSHA::sshaPasswordGen("test");
        $result = SSHA::sshaPasswordVerify($password,"test");
        $this->assertEquals(true, $result);
    }
}
