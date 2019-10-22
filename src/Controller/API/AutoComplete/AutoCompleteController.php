<?php


namespace App\Controller\API\AutoComplete;


use App\Model\Services\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AutoCompleteController extends AbstractFOSRestController
{
    /**
     * @Get("/autocomplete/uid")
     * @Security("is_granted('ROLE_elder')")
     * @param Request $request
     * @param UserRepository $userRepository
     */
    public function autocompleteUserUid(Request $request, UserRepository $userRepository)
    {
        $uid = $request->get("q");
        $users = $userRepository->findUsersByUidLike($uid);
        $uids = array();
        foreach ($users as $user) {
            array_push($uids, $user->getUid());
        }

        return View::create($uids, Response::HTTP_ACCEPTED);
    }
}