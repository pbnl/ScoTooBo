<?php

namespace App\Entity\LDAP;

use App\Model\Services\UserRepository;
use App\Model\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents a posixGroup object class, which is a subclass of Group
 *
 */
class PosixGroup extends LdapEntity
{

    static $mustFields = ["cn", "gidNumber"];
    static $uniqueIdentifier = "cn";

    protected $cn;


    /**
     * Array with all the DNs of the users who are members
     */
    protected $memberUid;

    /**
     * Unique gid for this group
     *
     * @Assert\Type("integer")
     *
     * @var int
     */
    protected $gidNumber;

    /**
     * Array with all members but as User objects
     * @var array
     */
    private $memberUserObjects = array();

    /**
     * @return mixed
     */
    public function getCn()
    {
        return $this->cn;
    }

    /**
     * @param mixed $cn
     */
    public function setCn($cn)
    {
        $this->cn = $cn;
    }

    /**
     * @return array
     */
    public function getMemberUserObjects()
    {
        if ($this->memberUserObjects === []) {
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
     *
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

    /**
     * Generates a Dn based on the OU and the givenName
     */
    protected function generateNewDn()
    {
        if ($this->getCn() == "") {
            throw new BadMethodCallException("Cant generate DN: cn is empty ('')");
        }
        return "cn=" . $this->getCn() . ",ou=Group,dc=pbnl,dc=de";
    }

    public function addUser(User $user)
    {
        if (!in_array($user->getDn(), $this->memberUid)) {
            array_push($this->memberUid, $user->getDn());
        }
    }

}
