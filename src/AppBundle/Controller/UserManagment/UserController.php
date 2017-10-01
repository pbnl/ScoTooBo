<?php

namespace AppBundle\Controller\UserManagment;

use AppBundle\Model\Filter;
use AppBundle\Model\Services\GroupNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/users/show/all", name="showAllUser")
     * @Security("has_role('ROLE_elder')")
     */
    public function showAllUser(Request $request)
    {
        //Create search form
        $defaultData = array();
        $userSearchForm = $this->createFormBuilder($defaultData)
            ->add("filterOption",ChoiceType::class,array(
                "choices"=>array("username"=>"filterByName","group"=>"filterByGroup"),
                'label'=>false,
                'required' => false,
                'data'=>"filterByName"))
            ->add("filterText",TextType::class,array(
                "attr"=>["placeholder"=>"search"],
                'label'=>false,
                'required' => false))
            ->add("send",SubmitType::class,array(
                "label"=>"search",
                "attr"=>["class"=>"btn btn-lg btn-primary btn-block"]))
            ->setMethod("get")
            ->getForm();

        $filter = new Filter();

        //Handel the form input
        $userSearchForm->handleRequest($request);
        if($userSearchForm->isSubmitted() && $userSearchForm->isValid()) {
            $data = $userSearchForm->getData();
            $filter->addFilter($data["filterOption"], $data["filterText"]);
        }

        $userRepo = $this->get("data.userRepository");
        try {
            $users = $userRepo->getAllUsers($filter);
        }
        catch (GroupNotFoundException $e)
        {
            $users = [];
            $this->addFlash("info",$e->getMessage());
        }

        return $this->render('userManagment/showAllUsers.html.twig', [
            "peopleSearchForm" => $userSearchForm->createView(),
            "users"=>$users,
        ]);
    }
}
