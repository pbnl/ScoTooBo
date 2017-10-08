<?php

namespace AppBundle\Controller\EventManagement;

use AppBundle\Entity\Event;
use AppBundle\Entity\EventAttend;
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
            ->add('save', SubmitType::class, array(
                'label' => 'Event.add.Submit',
                "attr"=>["class"=>"btn btn-primary"]))
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
        $this->addFlash("info", "This function is comming soon!");
        return $this->redirectToRoute("showAllEvents");
    }

    /**
     * @Route("/events/remove", name="removeEvent")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeEvent(Request $request)
    {
        $this->addFlash("info", "This function is comming soon!");
        return $this->redirectToRoute("showAllEvents");
    }

    /**
     * @Route("/events/invitationLink/generate/{id}", name="generateInvitationLink")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateInvitationLink($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($id);
        if ($event) {
            $link = $event->getInvitationLink();

            $length = rand(
                $this->container->getParameter('events.invitationlink.length.min'),
                $this->container->getParameter('events.invitationlink.length.max')
            );
            $random_string = $this->generateRandomString($length);

            $event->setInvitationLink($random_string);
            $em->flush();

            if ($link=='NULL') {
                $this->addFlash("success", "Der Einladungslink für ".$event->getName()." wurde geändert.");
            } else {
                $this->addFlash("success", "Der Einladungslink für ".$event->getName()." wurde erzeugt.");
            }
        } else {
            $this->addFlash("error", "Event mit der Id $id wurde nicht gefunden!");
        }
        return $this->redirectToRoute("showAllEvents");
    }

    /**
     * @Route("/events/attend/{invitationLink}", name="attendInvitationLink")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function attendInvitationLink($invitationLink)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->findOneBy(array('invitationLink' => $invitationLink));
        if ($event) {
            $loggedInUser_Uid = '';
            $loggedInUser_Stamm = '';
            if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();
                $loggedInUser_Uid = $loggedInUser->getUid();
                $loggedInUser_Stamm = $loggedInUser->getStamm();
            }

            $eventAttend = new EventAttend();
            $eventAttend->setEventId($event->getId());

            $form = $this->createFormBuilder($eventAttend)
                ->add('firstname', TextType::class)
                ->add('lastname', TextType::class)
                ->add('address_street', TextType::class)
                ->add('address_nr', TextType::class)
                ->add('address_plz', IntegerType::class)
                ->add('address_city', TextType::class)
                ->add('stamm', TextType::class)
                ->add('group', TextType::class)
                ->add('comment', TextareaType::class)
                ->add('save', SubmitType::class, array('label' => 'Create Post'))
                ->getForm();

            return $this->render('eventManagement/attendInvitationLink.html.twig', array(
                "loggedInUser_Uid"=>$loggedInUser_Uid,
                "loggedInUser_Stamm"=>$loggedInUser_Stamm,
                "event"=>$event,
                "registrationAttendInvitationLink"=>$form->createView(),
            ));
        } else {
            $this->addFlash("error", "Dieser Einladungslink ist leider nicht mehr gültig!");
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @param int $length
     * @param string $characters
     * @return string
     */
    private function generateRandomString($length = 16, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
