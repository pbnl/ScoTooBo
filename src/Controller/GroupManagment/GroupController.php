<?php

namespace App\Controller\GroupManagment;

use App\Model\Filter;
use App\Model\Services\GroupNotFoundException;
use App\Model\Services\GroupRepository;
use App\Model\Services\UserDoesNotExistException;
use App\Model\Services\UserRepository;
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
