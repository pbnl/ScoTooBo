<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 31.08.17
 * Time: 10:47
 */

namespace AppBundle\Model\Entity\LDAP;


use Ucsf\LdapOrmBundle\Annotation\Ldap\ArrayField;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Attribute;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Dn;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Must;
use Ucsf\LdapOrmBundle\Annotation\Ldap\ObjectClass;
use Ucsf\LdapOrmBundle\Annotation\Ldap\SearchDn;
use Ucsf\LdapOrmBundle\Annotation\Ldap\UniqueIdentifier;
use Ucsf\LdapOrmBundle\Entity\Ldap\LdapEntity;

/**
 * Represents a pbnlMailAlias object class
 *
 * @ObjectClass("pbnlMailAlias")
 * @SearchDn("ou=Forward,dc=pbnl,dc=de")
 * @Dn("mail={{entity.mail}},ou=forward,dc=pbnl,dc=de")
 * @UniqueIdentifier("mail")
 */
class PbnlMailAlias extends LdapEntity
{

    /**
     * The addresses of the mailing list
     *
     * @Attribute("forward")
     * @Must()
     * @ArrayField()
     * @var string
     */
    protected $forward = array();

    /**
     * The address of the list
     *
     * @Attribute("mail")
     * @Must()
     * @var string
     */
    protected $mail = "";



    /**
     * @return mixed
     */
    public function getForward()
    {
        return $this->forward;
    }

    /**
     * @param mixed $forward
     */
    public function setForward($forward)
    {
        $this->forward = $forward;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }


}