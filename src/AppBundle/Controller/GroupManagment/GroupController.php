<?php

namespace AppBundle\Controller\GroupManagment;

use AppBundle\Model\Filter;
use AppBundle\Model\Services\GroupNotFoundException;
use AppBundle\Model\Services\GroupRepository;
use AppBundle\Model\Services\UserDoesNotExistException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends Controller
{
    /**
     * @Route("/groups/show/all", name="showAllGroups")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllUser(Request $request)
    {
        $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();

        $groupRepo = $this->get("data.groupRepository");

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
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetailGroup(Request $request)
    {
        $groupCn = $request->get("groupCn", "");

        $groupRepo = $this->get("data.groupRepository");
        try {
            $group = $groupRepo->findByCn($groupCn);
        } catch (GroupNotFoundException $e) {
            $this->addFlash("error", "Group not found");
            return $this->redirectToRoute("showAllGroups");
        }

        //Allow access if you are a member of this group
        $this->denyAccessUnlessGranted(
            'ROLE_'.$group->getCn(),
            null,
            'You are not allowed to see the group ' . $group->getCn()
        );

        $userRepo = $this->get("data.userRepository");
        try {
            $group->fetchGroupMemberUserObjects($userRepo);
        } catch (UserDoesNotExistException $e) {
            $this->addFlash("error", $e->getMessage());
        }

        return $this->render("groupManagment/detailGroup.html.twig", array(
            "group" => $group,
        ));
    }
}
