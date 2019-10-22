<?php

namespace App\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperController extends AbstractController
{
    /**
     * @Route("/admin/showEmptyFullWidthPageWithNavbar", name="showEmptyFullWidthPageWithNavbar")
     * @Security("is_granted('ROLE_admin')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showEmptyFullWidthPageWithNavbar()
    {
        return $this->render("admin/showEmptyFullWidthPageWithNavbar.html.twig");
    }

    /**
     * @Route("/admin/showEmptyFullWidthPageWithoutNavbar", name="showEmptyFullWidthPageWithoutNavbar")
     * @Security("is_granted('ROLE_admin')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showEmptyFullWidthPageWithoutNavbar()
    {
        return $this->render("admin/showEmptyFullWidthPageWithoutNavbar.html.twig");
    }

    /**
     * @Route("/admin/showEmptyPageWithSidebarWithNavbar", name="showEmptyPageWithSidebarWithNavbar")
     * @Security("is_granted('ROLE_admin')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showEmptyPageWithSidebarWithNavbar()
    {
        return $this->render("admin/showEmptyPageWithSidebarWithNavbar.html.twig");
    }
}
