<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 11.11.17
 * Time: 20:33
 */

namespace Tests\AppBundle\LdapComponent;


use AppBundle\Model\LdapComponent\LdapConnection;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Monolog\Logger;

class LdapConnectionTest extends TestCase
{
    public function testOpenConnection()
    {
        $params = array();
        $params["uri"] = "127.0.0.1";
        $params["port"] = "389";
        $params["use_tls"] = true;
        $params["password"] = "secret";
        $params["bind_dn"] = "toBind";

        $con = $this->createMock(LdapConnection::class);
        $con->expects($this->once())->method("openConnection");
        $con->openConnection();
    }

    public function testOpenRealConnection()
    {
        $params = array();
        $params["uri"] = "127.0.0.1";
        $params["port"] = "389";
        $params["use_tls"] = false;
        $params["password"] = "admin";
        $params["bind_dn"] = "cn=admin,dc=pbnl,dc=de";

        $con = new LdapConnection($params["uri"],$params["port"],$params["use_tls"],$params["password"],$params["bind_dn"], new Logger(""));
        $result = $con->openConnection();

        $this->assertTrue($result);
    }
}