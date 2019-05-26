<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 27.11.18
 * Time: 21:40
 */

namespace AppBundle\Model\Services;


use AppBundle\Entity\LDAP\PosixGroup;
use AppBundle\Model\LdapComponent\PbnlLdapEntityManager;
use AppBundle\Model\User;

class UserLazyLoader
{

    /**
     * UserLazyLoader constructor.
     */
    public function __construct(PbnlLdapEntityManager $ldapEntityManager)
    {
        $this->ldapEntityManager = $ldapEntityManager;
        $this->ldapGroupRepo = $ldapEntityManager->getRepository(PosixGroup::class);
    }

    public function loadRoles(User $user) {
        $roles = $this->getRolesOfPbnlAccount($user);
        array_push($roles, "ROLE_USER");
        $user->setRoles($roles);
    }

    /**
     * Returns an array with with all roles of a PbnlAccount ['ROLE_Groupname']
     * It tries to find groups in the ldap database and check if the dn og the PbnlAccount is a member of this group
     *
     * @param PbnlAccount $ldapPbnlAccount
     * @return array
     */
    private function getRolesOfPbnlAccount(User $user)
    {
        $roles = array();
        $filter = ["memberUid" => $user->getDn()];
        $memberGroups = $this->ldapGroupRepo->findByComplex($filter);

        /** @var  $group PosixGroup */
        foreach ($memberGroups as $group) {
            array_push($roles, "ROLE_".$group->getCn());
        }

        return $roles;
    }
}