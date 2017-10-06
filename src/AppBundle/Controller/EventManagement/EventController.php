<?php

namespace AppBundle\Controller\EventManagement;

use AppBundle\Entity\Event;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends Controller
{
    /**
     * @Route("/events/show/all", name="showAllEvents")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllEvents(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Event::class);
        $events = $repository->findAll();

        return $this->render('eventManagement/showAllEvents.html.twig', [
#            "peopleSearchForm" => $userSearchForm->createView(),
            "events"=>$events,
        ]);
    }



    /**
     * @Route("/events/add", name="addEvent")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addEvent(Request $request)
    {
        $event = new Event();

        $addAnEventForm = $this->createFormBuilder($event)
            ->add('Name', TextType::class, array(
                "attr" => ["placeholder" => "Event.add.Name"],
                'label' => "Event.add.Name",
                'empty_data' => '',
                "required" => true))
            ->add('Description', TextareaType::class, array(
                "attr" => ["placeholder" => "Event.add.Beschreibung"],
                'label' => "Event.add.Beschreibung",
                'empty_data' => '',
                "required" => true))
            ->add('PriceInCent', IntegerType::class, array(
                "attr" => ["placeholder" => "Event.add.PriceInCent"],
                'label' => "Event.add.PriceInCent",
                'empty_data' => '',
                "required" => true))
            ->add('DateFrom', DateTimeType::class, array(
                'attr' => ["placeholder" => "Event.add.DateFrom"],
                'label' => "Event.add.DateFrom",
                'empty_data' => '',
                // ToDo: bessere Auswahlmöglichkeit bieten
                "required" => true))
            ->add('DateTo', DateTimeType::class, array(
                'attr' => ["placeholder" => "Event.add.DateTo"],
                'label' => "Event.add.DateTo",
                'empty_data' => '',
                // ToDo: bessere Auswahlmöglichkeit bieten
                "required" => true))
            ->add('Place', TextareaType::class, array(
                "attr" => ["placeholder" => "Event.add.Place"],
                'label' => "Event.add.Place",
                'empty_data' => '',
                "required" => true))
            ->add('save', SubmitType::class, array('label' => 'Event.add.Submit'))
            ->getForm();

        $addAnEventForm->handleRequest($request);

        if ($addAnEventForm->isSubmitted() && $addAnEventForm->isValid()) {
            $event_data = $addAnEventForm->getData();
            $em = $this->getDoctrine()->getManager();

            $em->persist($event_data); // tells Doctrine you want to (eventually) save the Product (no queries yet)
            $em->flush(); // actually executes the queries (i.e. the INSERT query)

            $this->addFlash("success", "Event wurde mit der Id ".$event_data->getId()." erstellt.");
            return $this->redirectToRoute('showAllEvents');
        }

        return $this->render('eventManagement/addEvent.html.twig', array(
            'addAnEventForm' => $addAnEventForm->createView(),
        ));
    }

    /**
     * @Route("/events/detail", name="detailEvent")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailEvent(Request $request)
    {
        $this->addFlash("success", "This function is comming soon!");
        return $this->redirectToRoute("showAllEvents");
    }

    /**
     * @Route("/events/remove", name="removeEvent")
     * @param Request $request
     * @return Response
     */
    public function removeEvent(Request $request)
    {
        $this->addFlash("success", "This function is comming soon!");
        return $this->redirectToRoute("showAllEvents");
    }
}
