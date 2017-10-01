<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 06.09.17
 * Time: 18:33
 */

namespace AppBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="showDashboard")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDashboard(Request $request)
    {
        return $this->render('dashboard/showDashboard.html.twig', array(
        ));
    }
}
