<?php

namespace AppBundle\Controller\EventManagement;

use AppBundle\Entity\Event;
use DateTime;
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



    /**
     * @Route("/events/add", name="addEvent")
     * @Security("has_role('ROLE_stavo')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addEvent(Request $request)
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: createAction(EntityManagerInterface $em)
        $em = $this->getDoctrine()->getManager();

        $event = new Event();
        $event->setName("Test");
        $event->setDescription("qwertzu qwertz qwertzu");
        $event->setPriceInCent(rand(1,200));
        $date = new DateTime(rand(2000,2020).'-01-01');
        $event->setDateFrom($date);
        $event->setDateTo($date);
        $event->setPlace("at home");

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($event);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        $this->addFlash("success", "Saved new random event with id ".$event->getId());
        return $this->redirectToRoute("allEvents");
    }

    /**
     * @Route("/events/detail", name="detailEvent")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetailUser(Request $request)
    {
        return;
    }

    /**
     * @Route("/events/remove", name="removeEvent")
     * @param Request $request
     * @return Response
     */
    public function removeUser(Request $request)
    {
        return;
    }
}