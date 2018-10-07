<?php

namespace AppBundle\Model\Services;

use AppBundle\Entity\LDAP\PosixGroup;
use AppBundle\Model\Filter;
use AppBundle\Model\LdapComponent\PbnlLdapEntityManager;
use Monolog\Logger;
use Symfony\Component\Config\Tests\Util\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GroupRepository
{
    /**
     * A reference to the LdapEntityService to work with the ldap
     *
     * @var PbnlLdapEntityManager
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

    /**
     * @var \Ucsf\LdapOrmBundle\Ldap\EntityRepository
     */
    private $groupLdapRepository;

    const FILTERBYDNINGROUP = "filterByDnInGroup";


    /**
     * The ldapManager of the LDAPBundle
     *
     * @param Logger $logger
     * @param PbnlLdapEntityManager $ldapEntityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(Logger $logger, PbnlLdapEntityManager $ldapEntityManager, ValidatorInterface $validator)
    {
        $this->ldapEntityManager = $ldapEntityManager;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->groupLdapRepository = $this->ldapEntityManager->getRepository(PosixGroup::class);
    }

    /**
     * @param Filter $filter
     * @return array
     */
    public function findAllGroupsByComplexFilter(Filter $filter)
    {
        $allGroups = $this->groupLdapRepository->findAll();
        $phpFilteredGroups = $this->getGroupsThatFullFillPhpFilter($allGroups, $filter);

        return $phpFilteredGroups;
    }

    /**
     * @param array $groups
     * @param Filter $filter
     * @return array
     */
    private function getGroupsThatFullFillPhpFilter(array $groups, Filter $filter)
    {
        $i = 0;
        foreach ($filter->getFilterAttributes() as $filterAttribute)
        {
            if ($filterAttribute == GroupRepository::FILTERBYDNINGROUP) {
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
            if ($group->isDnMember($dn)) {
                array_push($groupsThatFullFillFilter, $group);
            }
        }
        return $groupsThatFullFillFilter;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->findAllGroupsByComplexFilter(new Filter());
    }

    public function findByCn($cn)
    {
        $group = $this->groupLdapRepository->findByCn($cn);
        if ($group == []) {
            throw new GroupNotFoundException("We cant find the group ".$cn);
        }
        return $group[0];
    }

    public function updateGroup(PosixGroup $group)
    {
        $this->ldapEntityManager->persist($group);
    }
}
