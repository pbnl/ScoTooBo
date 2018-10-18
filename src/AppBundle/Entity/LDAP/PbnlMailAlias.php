<?php

namespace AppBundle\Entity\LDAP;

use BadMethodCallException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a pbnlMailAlias object class, which is a subclass of LdapEntity
 *
 */
class PbnlMailAlias extends LdapEntity
{

    public static $mustFields = [];
    public static $uniqueIdentifier = "mail";

    /**
     * @var string mail The mail of the mailinglist or forward
     *
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = false
     * )
     */
    protected $mail;


    /**
     * @var array forward An array of all mail addresses of the mailinglist or forward
     *
     * * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\Email(
     *      message = "The email '{{ value }}' is not a valid email.",
     *      checkMX = false
     *     )
     * })
     */
    protected $forward = array();

    /**
     * PbnlMailAlias constructor.
     * @param string $mail
     * @param array $forward
     */
    public function __construct($mail = "", array $forward = [])
    {
        $this->mail = $mail;
        $this->forward = $forward;
    }

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
        if ($this->getMail() == "")
        {
            throw new BadMethodCallException("Cant generate DN: mail is empty ('')");
        }
        return "mail=".$this->getMail().",ou=Forward,dc=pbnl,dc=de";
    }

    public function __toString()
    {
        return $this->getMail();
    }
}
