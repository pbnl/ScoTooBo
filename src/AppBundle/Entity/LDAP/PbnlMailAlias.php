<?php

namespace AppBundle\Entity\LDAP;

use BadMethodCallException;

/**
 * Represents a pbnlMailAlias object class, which is a subclass of LdapEntity
 *
 */
class PbnlMailAlias extends LdapEntity
{

    static $mustFields = [];
    static $uniqueIdentifier = "mail";

    /**
     * @var string mail The mail of the mailinglist or forward
     *
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    protected $mail;


    /**
     * @var array forward An array of all mail addresses of the mailinglist or forward
     */
    protected $forward = array();

    /**
     * @return string mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     *
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return array forward
     */
    public function getForward()
    {
        return $this->forward;
    }

    /**
     * @param array forward $forward
     */
    public function setForward($forward)
    {
        $this->forward = $forward;
    }



    /**
     * Generates a Dn based on the mail
     */
    protected function generateNewDn()
    {
        if($this->getMail() == "")
        {
            throw new BadMethodCallException("Cant generate DN: mail is empty ('')");
        }
        return "mail=".$this->getMail().",ou=Forward,dc=pbnl,dc=de";
    }

}
