<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DeveloperController extends Controller
{
    /**
     * @Route("/admin/showEmptyFullWidthPageWithNavbar", name="showEmptyFullWidthPageWithNavbar")
     * @Security("has_role('ROLE_admin')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showEmptyFullWidthPageWithNavbar()
    {
        return $this->render("admin/showEmptyFullWidthPageWithNavbar.html.twig");
    }

    /**
     * @Route("/admin/showEmptyFullWidthPageWithoutNavbar", name="showEmptyFullWidthPageWithoutNavbar")
     * @Security("has_role('ROLE_admin')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showEmptyFullWidthPageWithoutNavbar()
    {
        return $this->render("admin/showEmptyFullWidthPageWithoutNavbar.html.twig");
    }

    /**
     * @Route("/admin/showEmptyPageWithSidebarWithNavbar", name="showEmptyPageWithSidebarWithNavbar")
     * @Security("has_role('ROLE_admin')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showEmptyPageWithSidebarWithNavbar()
    {
        return $this->render("admin/showEmptyPageWithSidebarWithNavbar.html.twig");
    }
}
