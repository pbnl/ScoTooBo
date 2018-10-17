<?php

namespace AppBundle\Controller\MailAlias;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MailAliasController extends Controller
{
    /**
     * @Route("/mailAlias/show/all", name="showAllMailAlias")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showMailAlias(Request $request)
    {
        $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();

        $mailAliasRepo = $this->get("data.mailAliasRepository");

        $allmailAlias = $mailAliasRepo->findAll();
        $allowdMailAlias = array();
        foreach($allmailAlias as $mailAlias) {
            if($this->isGranted( "view", $mailAlias)) {
                array_push($allowdMailAlias, $mailAlias);
            }
        }

        return $this->render('mailAliasManagment/showAllMailAlias.html.twig', [
            "mailAliasList" => $allowdMailAlias
        ]);
    }


    /**
     * @Route("/mailAlias/detail", name="detailMailAlias")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailMailAlias(Request $request)
    {
        $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();

        $mailAliasRepo = $this->get("data.mailAliasRepository");

        $mailAlias = $mailAliasRepo->findByMail($request->get("mailAlias"));

        $this->denyAccessUnlessGranted("view", $mailAlias );

        return $this->render('mailAliasManagment/detailMailAlias.html.twig', [
            "mailAlias" => $mailAlias
        ]);
    }

}
