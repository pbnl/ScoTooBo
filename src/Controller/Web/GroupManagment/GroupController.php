<?php

namespace App\Controller\Web\GroupManagment;

use App\Entity\LDAP\PosixGroup;
use App\Entity\LDAP\UserAlreadyInGroupException;
use App\Entity\LDAP\UserIsNotAMemberException;
use App\Forms\AddUserToGroupForm;
use App\Model\Filter;
use App\Model\Services\GroupNotFoundException;
use App\Model\Services\GroupRepository;
use App\Model\Services\UserDoesNotExistException;
use App\Model\Services\UserRepository;
use App\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    /**
     * @Route("/groups/show/all", name="showAllGroups")
     * @Security("is_granted('ROLE_elder')")
     * @param Request $request
     * @param GroupRepository $groupRepo
     * @return Response
     */
    public function showAllGroups(Request $request, GroupRepository $groupRepo): Response
    {
        $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();

        $filter = new Filter();
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_buvo')) {
            $filter->addFilter(GroupRepository::FILTERBYDNINGROUP, $loggedInUser->getDn());
        }

        $groups = $groupRepo->findAllGroupsByComplexFilter($filter);

        return $this->render('groupManagment/showAllGroups.html.twig', [
            "groups" => $groups
        ]);
    }

    /**
     * @Route("/groups/detail", name="showDetailGroup")
     * @Security("is_granted('ROLE_elder')")
     * @param Request $request
     * @param UserRepository $userRepo
     * @param GroupRepository $groupRepo
     * @return Response
     */
    public function showDetailGroup(Request $request, UserRepository $userRepo, GroupRepository $groupRepo): Response
    {
        $groupCn = $request->get("groupCn", "");

        try {
            $group = $groupRepo->findByCn($groupCn);
        } catch (GroupNotFoundException $e) {
            $this->addFlash("error", "Group not found");
            return $this->redirectToRoute("showAllGroups");
        }

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_buvo')) {
            //Allow access if you are a member of this group
            $this->denyAccessUnlessGranted(
                'ROLE_' . $group->getCn(),
                null,
                'You are not allowed to see the group ' . $group->getCn()
            );
        }
        $newMember = new User("", "", "", null);
        $addMemberForm = $this->createForm(AddUserToGroupForm::class, $newMember);

        $addMemberForm->handleRequest($request);
        if ($addMemberForm->isSubmitted() && $addMemberForm->isValid()) {
            try {
                $newMember = $addMemberForm->getData();
                //TODO: Rename it to persist group
                $uid = $newMember->getUid();
                $newMember = $userRepo->getUserByUid($uid);
                $group->addUser($newMember);
                $groupRepo->updateGroup($group);
                $this->addFlash("success", "Added user with uid $uid to group $groupCn");
            } catch (UserDoesNotExistException $e) {
                $this->addFlash("error", "The user $uid does not exist");
            } catch (UserAlreadyInGroupException $e) {
                $this->addFlash("error", "The user $uid is already in the group $groupCn");
            }
        }

        try {
            $group->fetchGroupMemberUserObjects($userRepo);
        } catch (UserDoesNotExistException $e) {
            $this->addFlash("error", $e->getMessage());
        }

        return $this->render("groupManagment/detailGroup.html.twig", array(
            "group" => $group,
            'addMemberForm' => $addMemberForm->createView(),
        ));
    }


    /**
     * @Route("/groups/{group_cn}/remove", name="deleteGroupmember")
     * @Security("is_granted('ROLE_elder')")
     * @param $group_cn
     * @param Request $request
     * @param UserRepository $userRepo
     * @param GroupRepository $groupRepo
     * @return Response
     */
    public function deleteGroupmember($group_cn, Request $request, UserRepository $userRepo, GroupRepository $groupRepo): Response
    {
        $user_uid = $request->get("uid", "");

        try {
            $group = $groupRepo->findByCn($group_cn);
        } catch (GroupNotFoundException $e) {
            $this->addFlash("error", "Group not found");
            return $this->redirectToRoute("showAllGroups");
        }

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_buvo')) {
            //Allow access if you are a member of this group
            $this->denyAccessUnlessGranted(
                'ROLE_' . $group->getCn(),
                null,
                'You are not allowed to edit the group ' . $group->getCn()
            );
        }
        try {
            $userToRemove = $userRepo->getUserByUid($user_uid);
            $group->removeUser($userToRemove);
            $groupRepo->updateGroup($group);
            $this->addFlash("success", "Removed user with uid $user_uid from group $group_cn");
        } catch (UserDoesNotExistException $e) {
            $this->addFlash("error", "The user with uid $user_uid does not exist");
        } catch (UserIsNotAMemberException $e) {
            $this->addFlash("error", "The user with uid $user_uid is not a member of the group $group_cn");
        }
        return $this->redirectToRoute("showDetailGroup", ["groupCn"=>$group_cn]);
    }
}
