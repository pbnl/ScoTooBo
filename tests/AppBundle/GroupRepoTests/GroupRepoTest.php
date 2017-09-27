<?php

namespace Tests\AppBundle\UserServiceTests;

use AppBundle\Model\Entity\LDAP\PosixGroup;
use AppBundle\Model\Filter;
use AppBundle\Model\Services\GroupRepository;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Ucsf\LdapOrmBundle\Ldap\LdapEntityManager;
use Ucsf\LdapOrmBundle\Repository\Repository;

class GroupRepoTest extends TestCase
{
    public function testGetAllGroupsByComplexFilter()
    {
        $group1 = new PosixGroup();
        $group1->setCn("test1");
        $group2 = new PosixGroup();
        $group2->setCn("test2");
        $group3 = new PosixGroup();
        $group3->setCn("test3");

        $groups = [$group1, $group2, $group3];

        $posixGroupRepo = $this->createMock(Repository::class);
        $posixGroupRepo->expects($this->any())
            ->method("findAll")
            ->willReturn($groups);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->withConsecutive(
                [$this->equalTo(PosixGroup::class)])
            ->willReturnOnConsecutiveCalls($posixGroupRepo);

        $groupRepo = new GroupRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());

        $actualGroups = $groupRepo->getAllGroupsByComplexFilter(new Filter());

        $this->assertEquals($groups, $actualGroups);
    }

    public function testGetAllGroupsByComplexFilterFilterByDn()
    {
        $group1 = new PosixGroup();
        $group1->setCn("test1");
        $group1->setMemberUid([]);
        $group2 = new PosixGroup();
        $group2->setCn("test2");
        $group2->setMemberUid(["pleaseFindMe","something1"]);
        $group3 = new PosixGroup();
        $group3->setCn("test3");
        $group3->setMemberUid(["something2"]);

        $groups = [$group2];

        $posixGroupRepo = $this->createMock(Repository::class);
        $posixGroupRepo->expects($this->any())
            ->method("findAll")
            ->willReturn($groups);

        $ldapEntityManager = $this->createMock(LdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->withConsecutive(
                [$this->equalTo(PosixGroup::class)])
            ->willReturnOnConsecutiveCalls($posixGroupRepo);

        $groupRepo = new GroupRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());

        $filter = new Filter();
        $filter->addFilter(GroupRepository::filterByDnInGroup, "pleaseFindMe");
        $actualGroups = $groupRepo->getAllGroupsByComplexFilter($filter);

        $this->assertEquals($groups, $actualGroups);
    }
}
