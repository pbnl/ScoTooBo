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

    public function __construct($uri, $tls, $password, $bind)
    {
        $this->uri = $uri;
        $this->use_tls = $tls;
        $this->password = $password;
        $this->bind_dn = $bind;
    }

    /**
     * Opens a connection to a ldap server
     *
     * @param string $server
     * @param int $port
     */
    private function connect(string $server, int $port = 389)
    {
        $this->ldapConnection = ldap_connect($server, $port);
        if($this->ldapConnection == FALSE)
        {
            throw new LdapConnectException("Cant connect to $server on port $port");
        }
    }

    private function bind($ldapConnection, string $dn, string $password)
    {
        ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
        $ldapbind = ldap_bind($ldapConnection, $dn, $password);
        if($ldapbind == FALSE)
        {
            throw new LdapBindException("Cannot bind to server with dn $dn using password yes");
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
        $this->connect($this->uri);
        $result = $this->bind($this->ldapConnection, $this->bind_dn, $this->password);

        return $result;
    }

    public function closeConnection()
    {
        return ldap_close($this->ldapConnection);
    }

    public function ldap_search(string $base_dn, string $filter)
    {
        return ldap_search($this->ldapConnection, $base_dn, $filter);
    }

    public function ldap_add(string $dn, array $element)
    {
        return ldap_add($this->ldapConnection, $dn, $element);
    }

    public function ldap_delete($dn)
    {
        return ldap_delete($this->ldapConnection, $dn);
    }

    public function ldap_mod_add(string $dn, array $element)
    {
        return ldap_mod_add($this->ldapConnection, $dn, $element);
    }

    public function ldap_mod_del(string $dn, array $element)
    {
        return ldap_mod_del($this->ldapConnection, $dn, $element);
    }

    public function ldap_modify(string $dn, array $entry)
    {
        return ldap_modify($this->ldapConnection, $dn, $entry);
    }

    public function ldap_get_entries($result)
    {
        return ldap_get_entries($this->ldapConnection, $result);
    }
}