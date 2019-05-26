<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 11.11.17
 * Time: 20:06
 */

namespace AppBundle\Model\LdapComponent;


class LdapConnection
{
    private $ldapConnection;
    private $uri;
    private $use_tls;
    private $password;
    private $bind_dn;
    private $port;

    public function __construct($uri, $port, $tls, $password, $bind, $logger)
    {
        $this->uri = $uri;
        $this->port = $port;
        $this->use_tls = $tls;
        $this->password = $password;
        $this->bind_dn = $bind;
        $this->logger = $logger;
    }

    /**
     * Opens a connection to a ldap server
     *
     * @param string $server
     * @param int $port
     */
    private function connect(string $server, int $port = 389)
    {
        if ($this->use_tls == "true") {
            $this->ldapConnection = @ldap_connect("ldaps://$server:$port");
        }
        else {
            $this->ldapConnection = @ldap_connect("ldap://$server:$port");
        }

        if($this->ldapConnection == FALSE)
        {
            throw new LdapConnectException("Cant connect to $server on port $port: ".
                ldap_error($this->ldapConnection));
        }
    }

    private function bind($ldapConnection, string $dn, string $password)
    {
        ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
        $ldapbind = @ldap_bind($ldapConnection, $dn, $password);
        if($ldapbind == FALSE)
        {
            throw new LdapBindException("Cannot bind to server with dn $dn using password yes: ".
                ldap_error($this->ldapConnection));
        }
        return $ldapbind;
    }

    /**
     * Connects the object to an ldap server
     * Uses the data in the paramters
     *
     */
    public function openConnection()
    {
        $this->connect($this->uri, $this->port);
        $result = $this->bind($this->ldapConnection, $this->bind_dn, $this->password);

        return $result;
    }

    public function closeConnection()
    {
        return ldap_close($this->ldapConnection);
    }

    public function ldap_search(string $base_dn, string $filter)
    {
        $ret = ldap_search($this->ldapConnection, $base_dn, $filter);
        if(!$ret) {
            ldap_get_option($this->ldapConnection, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err);
            $this->logger->error("LDAP ERROR: $err");
        }
        return $ret;
    }

    public function ldap_add(string $dn, array $element)
    {
        $ret = ldap_add($this->ldapConnection, $dn, $element);
        if(!$ret) {
            ldap_get_option($this->ldapConnection, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err);
            $this->logger->error("LDAP ERROR: $err");
        }
        return $ret;
    }

    public function ldap_delete($dn)
    {
        $ret = ldap_delete($this->ldapConnection, $dn);
        if(!$ret) {
            ldap_get_option($this->ldapConnection, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err);
            $this->logger->error("LDAP ERROR: $err");
        }
        return $ret;
    }

    public function ldap_mod_add(string $dn, array $element)
    {
        $ret = ldap_mod_add($this->ldapConnection, $dn, $element);
        if(!$ret) {
            ldap_get_option($this->ldapConnection, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err);
            $this->logger->error("LDAP ERROR: $err");
        }
        return $ret;
    }

    public function ldap_mod_del(string $dn, array $element)
    {
        $ret = ldap_mod_del($this->ldapConnection, $dn, $element);
        if(!$ret) {
            ldap_get_option($this->ldapConnection, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err);
            $this->logger->error("LDAP ERROR: $err");
        }
        return $ret;
    }

    public function ldap_modify(string $dn, array $entry)
    {
        $ret = ldap_modify($this->ldapConnection, $dn, $entry);
        if(!$ret) {
            ldap_get_option($this->ldapConnection, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err);
            $this->logger->error("LDAP ERROR: $err");
        }
        return $ret;
    }

    public function ldap_get_entries($result)
    {
        $ret = ldap_get_entries($this->ldapConnection, $result);
        if(!$ret) {
            ldap_get_option($this->ldapConnection, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err);
            $this->logger->error("LDAP ERROR: $err");
        }
        return $ret;
    }

    public function getError()
    {
        return ldap_error($this->ldapConnection);
    }
}