<?php

namespace App\Tests\UserServiceTests;

use App\Entity\LDAP\PosixGroup;
use App\Model\Filter;
use App\Model\LdapComponent\PbnlLdapEntityManager;
use App\Model\LdapComponent\Repositories\Repository;
use App\Model\Services\GroupRepository;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

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

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->withConsecutive(
                [$this->equalTo(PosixGroup::class)])
            ->willReturnOnConsecutiveCalls($posixGroupRepo);

        $groupRepo = new GroupRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());

        $actualGroups = $groupRepo->findAllGroupsByComplexFilter(new Filter());

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

        $ldapEntityManager = $this->createMock(PbnlLdapEntityManager::class);
        $ldapEntityManager->expects($this->any())
            ->method("getRepository")
            ->withConsecutive(
                [$this->equalTo(PosixGroup::class)])
            ->willReturnOnConsecutiveCalls($posixGroupRepo);

        $groupRepo = new GroupRepository(new Logger("main"), $ldapEntityManager, Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator());

        $filter = new Filter();
        $filter->addFilter(GroupRepository::FILTERBYDNINGROUP, "pleaseFindMe");
        $actualGroups = $groupRepo->findAllGroupsByComplexFilter($filter);

        $this->assertEquals($groups, $actualGroups);
    }
}
