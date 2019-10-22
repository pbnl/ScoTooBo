<?php

namespace App\Controller\Web\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="showDashboard")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDashboard(Request $request)
    {
        return $this->render('dashboard/showDashboard.html.twig', array());
    }
}
