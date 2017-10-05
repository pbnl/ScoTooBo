<?php

namespace Tests\AppBundle\Entity;


use AppBundle\Model\Entity\LDAP\PosixGroup;
use AppBundle\Model\Entity\LDAP\UsersNotFetched;
use AppBundle\Model\Services\UserRepository;
use AppBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PosixGroupTest extends WebTestCase
{
    /**
     * @issue #36
     */
    public function testGetMembersUserObjectsException()
    {
        $this->expectException(UsersNotFetched::class);

        $group = new PosixGroup();
        $group->getMemberUserObjects();
    }

    /**
     * @issue #36
     */
    public function testFetchGroupMemberUserObjects()
    {
        $user = new User("uid","asdf","asdfasdf",[]);

        $group = new PosixGroup();
        $group->setMemberUid(["dn"]);

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->expects($this->once())
            ->method("findUserByDn")
            ->with($this->equalTo("dn"))
            ->willReturn($user);

        $group->fetchGroupMemberUserObjects($userRepo);

        $expectedUserObjectArray = ["dn"=>$user];

        $actualUserObjectArray = $group->getMemberUserObjects();

        $this->assertEquals($expectedUserObjectArray, $actualUserObjectArray);
    }
}