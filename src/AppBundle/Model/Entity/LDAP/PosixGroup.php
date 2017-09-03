<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 31.08.17
 * Time: 10:34
 */

namespace AppBundle\Model\Entity\LDAP;


use Ucsf\LdapOrmBundle\Annotation\Ldap\ArrayField;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Attribute;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Dn;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Must;
use Ucsf\LdapOrmBundle\Annotation\Ldap\ObjectClass;
use Ucsf\LdapOrmBundle\Annotation\Ldap\SearchDn;
use Ucsf\LdapOrmBundle\Annotation\Ldap\UniqueIdentifier;
use Ucsf\LdapOrmBundle\Entity\Ldap\Group;

/**
 * Represents a posixGroup object class, which is a subclass of Group
 *
 * @ObjectClass("posixGroup")
 * @SearchDn("ou=group,dc=pbnl,dc=de")
 * @Dn("cn={{entity.cn}},ou=group,dc=pbnl,dc=de")
 * @UniqueIdentifier("cn")
 */
class PosixGroup extends Group
{

    /**
     * @Attribute("cn")
     * @Must()
     */
    protected $cn;

    /**
     * Array with all the DNs of the users who are members
     *
     * @Attribute("memberUid")
     * @ArrayField()
     * @Must()
     */
    protected $memberUid;

    /**
     * Unique gid for this group
     *
     * @Attribute("gidNumber")
     */
    protected $gidNumber;


    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getMemberUid()
    {
        return $this->memberUid;
    }

    /**
     * @param mixed $memberUid
     */
    public function setMemberUid($memberUid)
    {
        $this->memberUid = $memberUid;
    }

    /**
     * @return mixed
     */
    public function getGidNumber()
    {
        return $this->gidNumber;
    }

    /**
     * @param mixed $gidNumber
     */
    public function setGidNumber($gidNumber)
    {
        $this->gidNumber = $gidNumber;
    }

}