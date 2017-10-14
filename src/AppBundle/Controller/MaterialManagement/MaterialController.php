<?php

namespace AppBundle\Controller\MaterialManagement;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MaterialController extends Controller
{
    /**
     * @Route("/material/show/all", name="showAllMaterial")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllMaterial(Request $request)
    {
        return $this->render('materialManagement/showAllMaterial.html.twig', [
#            "events"=>$events,
        ]);
    }

    /**
     * @Route("/material/add", name="addMaterial")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMaterial(Request $request)
    {
        $this->addFlash("info", "This function is comming soon!");
        return $this->redirectToRoute("showAllMaterial");
    }

    /**
     * @Route("/material/detail", name="detailMaterial")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailMaterial(Request $request)
    {
        $this->addFlash("info", "This function is comming soon!");
        return $this->redirectToRoute("showAllMaterial");
    }

    /**
     * @Route("/material/remove", name="removeMaterial")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeMaterial(Request $request)
    {
        $this->addFlash("info", "This function is comming soon!");
        return $this->redirectToRoute("showAllMaterial");
    }
}
