<?php

namespace AppBundle\Model\Services;


use AppBundle\Model\Entity\LDAP\PosixGroup;
use AppBundle\Model\Filter;
use Monolog\Logger;
use Symfony\Component\Config\Tests\Util\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Ucsf\LdapOrmBundle\Ldap\LdapEntityManager;

class GroupRepository
{
    /**
     * A reference to the LdapEntityService to work with the ldap
     *
     * @var LdapEntityManager
     */
    private $ldapEntityManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Validator
     */
    private $validator;

    const filterByDnInGroup = "filterByDnInGroup";

    /**
     * The ldapManager of the LDAPBundle
     *
     * @param Logger $logger
     * @param LdapEntityManager $ldapEntityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(Logger $logger, LdapEntityManager $ldapEntityManager, ValidatorInterface $validator)
    {
        $this->ldapEntityManager = $ldapEntityManager;
        $this->logger = $logger;
        $this->validator = $validator;
    }

    /**
     * @param Filter $filter
     * @return array
     */
    public function getAllGroupsByComplexFilter(Filter $filter)
    {
        $groupLdapRepository = $this->ldapEntityManager->getRepository(PosixGroup::class);
        $allGroups = $groupLdapRepository->findAll();
        $phpFilteredGroups = $this->getGroupsThatFullFillPhpFilter($allGroups, $filter);

        return $phpFilteredGroups;
    }

    /**
     * @param array $groups
     * @param Filter $filter
     * @return array
     */
    private function getGroupsThatFullFillPhpFilter(Array $groups, Filter $filter)
    {
        $i = 0;
        foreach ($filter->getFilterAttributes() as $filterAttribute)
        {
            if($filterAttribute == GroupRepository::filterByDnInGroup) {
                $groups = $this->filterGroupsByDnInGroup($groups, $filter->getFilterTexts()[$i]);
            }
        }

        return $groups;
    }

    /**
     * @param array $groups
     * @param String $dn
     * @return array
     */
    private function filterGroupsByDnInGroup(array $groups, String $dn)
    {
        $groupsThatFullFillFilter = array();

        foreach ($groups as $group) {
            if($group->isDnMember($dn)) {
                array_push($groupsThatFullFillFilter, $group);
            }
        }
        return $groupsThatFullFillFilter;
    }
}
