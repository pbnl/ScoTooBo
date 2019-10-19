<?php

namespace App\Tests\Entity;


use App\Entity\LDAP\PosixGroup;
use App\Entity\LDAP\UsersNotFetched;
use App\Model\Services\UserRepository;
use App\Model\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PosixGroupTest extends WebTestCase
{
    public function testGetMembersUserObjectsException()
    {
        $this->expectException(UsersNotFetched::class);

        $group = new PosixGroup();
        $group->getMemberUserObjects();
    }

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
