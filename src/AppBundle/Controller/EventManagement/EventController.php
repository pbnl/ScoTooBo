<?php

namespace AppBundle\Controller\EventManagement;

use AppBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends Controller
{
    /**
     * @Route("/events/show/all", name="allEvents")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllEvents(Request $request)
    {
#        //Create search form
#        $defaultData = array();
#        $userSearchForm = $this->createFormBuilder($defaultData)
#            ->add("filterOption", ChoiceType::class, array(
#                "choices"=>array("username"=>"filterByUid", "group"=>"filterByGroup"),
#                'label'=>false,
#                'required' => false,
#                'data'=>"filterByUid"))
#            ->add("filterText", TextType::class, array(
#                "attr"=>["placeholder"=>"search"],
#                'label'=>false,
#                'required' => false))
#            ->add("send", SubmitType::class, array(
#                "label"=>"search",
#                "attr"=>["class"=>"btn btn-lg btn-primary btn-block"]))
#            ->setMethod("get")
#            ->getForm();
#
#        $filter = new Filter();
#
#        //Handel the form input
#        $userSearchForm->handleRequest($request);
#        if ($userSearchForm->isSubmitted() && $userSearchForm->isValid()) {
#            $data = $userSearchForm->getData();
#            $filter->addFilter($data["filterOption"], $data["filterText"]);
#        }

        $repository = $this->getDoctrine()->getRepository(Event::class);
        $events = $repository->findAll();

        return $this->render('eventManagement/showAllEvents.html.twig', [
#            "peopleSearchForm" => $userSearchForm->createView(),
            "events"=>$events,
        ]);
    }




}