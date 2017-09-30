<?php

namespace AppBundle\Model\Entity\LDAP;

use AppBundle\Model\Services\UserRepository;
use Ucsf\LdapOrmBundle\Annotation\Ldap\ArrayField;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Attribute;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Dn;
use Ucsf\LdapOrmBundle\Annotation\Ldap\Must;
use Ucsf\LdapOrmBundle\Annotation\Ldap\ObjectClass;
use Ucsf\LdapOrmBundle\Annotation\Ldap\SearchDn;
use Ucsf\LdapOrmBundle\Annotation\Ldap\UniqueIdentifier;
use Ucsf\LdapOrmBundle\Entity\Ldap\Group;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Type("integer")
     * @var int
     */
    protected $gidNumber;

    /**
     * Array with all members but as User objects
     * @var array
     */
    private $memberUserObjects = array();

    /**
     * @return array
     */
    public function getMemberUserObjects()
    {
        if($this->memberUserObjects == []) {
            throw new UsersNotFetched("You have to fetch the users first!");
        }
        return $this->memberUserObjects;
    }

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

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->cn;
    }

    /**
     * True, if the dn is a member of this group
     *
     * Uses the memberUid attribute of the ldapGroups
     *
     * @param String $dn
     * @return bool
     */
    public function isDnMember(String $dn)
    {
        if (in_array($dn, $this->getMemberUid())) {
            return true;
        }
        return false;
    }

    public function fetchGroupMemberUserObjects(UserRepository $userRepository)
    {
        foreach ($this->getMemberUid() as $dn) {
            $user = $userRepository->findUserByDn($dn);
            $this->memberUserObjects[$dn] = $user;
        }
    }
}
