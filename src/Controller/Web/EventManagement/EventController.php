<?php

namespace App\Controller\Web\EventManagement;

use App\Entity\Event;
use App\Entity\EventAttend;
use App\Forms\EventAttendForm;
use App\Model\Services\ReCaptchaService;
use App\Model\User;
use Exception;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    public function __construct(string $staemme)
    {
        $this->staemme = $staemme;
    }

    /**
     * @Route("/events/show/all", name="showAllEvents")
     * @Security("is_granted('ROLE_elder')")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function showAllEvents(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Event::class);
        $events = $repository->findAll();
        /* set default InvitationDate from now to the begin of the event */
        foreach ($events as $event) {
            if (is_null($event->getInvitationDateFrom())) {
                $event->setInvitationDateFrom(new \DateTime());
            }
            if (is_null($event->getInvitationDateTo())) {
                $event->setInvitationDateTo(
                    $event->getDateFrom()
                );
            }
        }

        return $this->render(
            'eventManagement/showAllEvents.html.twig',
            [
                "events" => $events,
                "possibleFormFields" => $this->getPossibleFormFields(),
            ]
        );
    }

    /**
     * @return array
     */
    private function getPossibleFormFields()
    {
        /**
         * NOTICE:
         * structure: tag, Label (german), checked, required field
         * The fields under tag name must exist in this class!
         */
        return array(
            array("name", "Name", true, true),
            array("email", "E-Mail", false, false),
            array("address", "Adresse", false, false),
            array("stamm", "Stamm", false, false),
            array("group", "Gruppe", false, false),
            array("eat", "Essenswünsche", false, false),
            array("comment", "Kommentar", true, false),
        );
    }


    /**
     * @Route("/events/add", name="addEvent")
     * @Security("is_granted('ROLE_elder')")
     * @param Request $request
     * @return Response
     */
    public function addEvent(Request $request)
    {
        $event = new Event();

        $addAnEventForm = $this->createFormBuilder($event)
            ->add(
                'Name',
                TextType::class,
                array(
                    "attr" => ["placeholder" => "Event.add.Name"],
                    'label' => "Event.add.Name",
                    'empty_data' => '',
                    "required" => true,
                )
            )
            ->add(
                'Description',
                TextareaType::class,
                array(
                    "attr" => ["placeholder" => "Event.add.Beschreibung"],
                    'label' => "Event.add.Beschreibung",
                    'empty_data' => '',
                    "required" => true,
                )
            )
            ->add(
                'PriceInCent',
                IntegerType::class,
                array(
                    "attr" => [
                        "placeholder" => "Event.add.PriceInCent",
                        "min" => 0,
                    ],
                    'label' => "Event.add.PriceInCent",
                    'empty_data' => '',
                    "required" => true,
                )
            )
            ->add(
                'DateFrom',
                DateTimeType::class,
                array(
                    'attr' => ["placeholder" => "Event.add.DateFrom"],
                    'label' => "Event.add.DateFrom",
                    'empty_data' => '',
                    // ToDo: bessere Auswahlmöglichkeit bieten
                    "required" => true,
                )
            )
            ->add(
                'DateTo',
                DateTimeType::class,
                array(
                    'attr' => ["placeholder" => "Event.add.DateTo"],
                    'label' => "Event.add.DateTo",
                    'empty_data' => '',
                    // ToDo: bessere Auswahlmöglichkeit bieten
                    "required" => true,
                )
            )
            ->add(
                'Place',
                TextareaType::class,
                array(
                    "attr" => ["placeholder" => "Event.add.Place"],
                    'label' => "Event.add.Place",
                    'empty_data' => '',
                    "required" => true,
                )
            )
            ->add(
                'save',
                SubmitType::class,
                array(
                    'label' => 'Event.add.Submit',
                    "attr" => ["class" => "btn btn-primary"],
                )
            )
            ->getForm();

        $addAnEventForm->handleRequest($request);

        if ($addAnEventForm->isSubmitted() && $addAnEventForm->isValid()) {
            $event_data = $addAnEventForm->getData();
            $em = $this->getDoctrine()->getManager();

            $em->persist($event_data); // tells Doctrine you want to (eventually) save the Product (no queries yet)
            $em->flush(); // actually executes the queries (i.e. the INSERT query)

            $this->addFlash("success", "Event wurde mit der Id " . $event_data->getId() . " erstellt.");

            return $this->redirectToRoute('showAllEvents');
        }

        return $this->render(
            'eventManagement/addEvent.html.twig',
            array(
                'addAnEventForm' => $addAnEventForm->createView(),
            )
        );
    }

    /**
     * @Route("/events/detail", name="detailEvent")
     * @Security("is_granted('ROLE_elder')")
     * @param Request $request
     * @return Response
     */
    public function detailEvent(Request $request)
    {
        $this->addFlash("info", "This function is comming soon!");

        return $this->redirectToRoute("showAllEvents");
    }

    /**
     * @Route("/events/remove", name="removeEvent")
     * @Security("is_granted('ROLE_elder')")
     * @param Request $request
     * @return Response
     */
    public function removeEvent(Request $request)
    {
        $this->addFlash("info", "This function is comming soon!");

        return $this->redirectToRoute("showAllEvents");
    }

    /**
     * @Route("/events/invitationLink/generate/{id}", name="generateInvitationLink")
     * @Security("is_granted('ROLE_elder')")
     * @param Request $request
     * @return Response
     */
    public function generateInvitationLink($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($id);
        if ($event) {
            $FormFields = $this->getPossibleFormFields();
            /* read POST values */
            for ($i = 0; $i < count($FormFields); $i++) {
                /* proofing if checkbox one is checked */
                $FormFields[$i][2] = false;
                if ($request->request->get($FormFields[$i][0] . "_show")) {
                    $FormFields[$i][2] = true;
                }

                /* proofing if checkbox two is checked */
                $FormFields[$i][3] = false;
                if ($request->request->get($FormFields[$i][0] . "_required") && $FormFields[$i][2]) {
                    $FormFields[$i][3] = true;
                }
            }
            $jsonFields = json_encode($FormFields);


            $link = $event->getInvitationLink();

            $random_string = Uuid::uuid4();

            if ($this->validateDate($request->request->get("InvitationDateFrom"))) {
                $invitationDateFrom = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $request->request->get("InvitationDateFrom")
                );
            } elseif (!is_null($event->getInvitationDateFrom())) {
                $invitationDateFrom = $event->getInvitationDateFrom();
                $this->addFlash(
                    "warning",
                    "Der neue Startzeitunkt wurde nicht verstanden und bleibt unverändert bei: " . $invitationDateFrom->format(
                        'Y-m-d H:i:s'
                    )
                );
            } else {
                $invitationDateFrom = new \DateTime();
                $this->addFlash(
                    "warning",
                    "Der neue Startzeitunkt wurde nicht verstanden und wurde auf jetzt gesetzt: " . $invitationDateFrom->format(
                        'Y-m-d H:i:s'
                    )
                );
            }
            if ($this->validateDate($request->request->get("InvitationDateTo"))) {
                $invitationDateTo = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $request->request->get("InvitationDateTo")
                );
            } elseif (!is_null($event->getInvitationDateTo())) {
                $invitationDateTo = $event->getInvitationDateTo();
                $this->addFlash(
                    "warning",
                    "Der neue Endzeitunkt wurde nicht verstanden und bleibt unverändert bei: " . $invitationDateTo->format(
                        'Y-m-d H:i:s'
                    )
                );
            } else {
                $invitationDateTo = $event->getDateFrom();
                $this->addFlash(
                    "warning",
                    "Der neue Endzeitunkt wurde nicht verstanden und wurde auf den Start des Events gesetzt: " . $invitationDateTo->format(
                        'Y-m-d H:i:s'
                    )
                );
            }


            $event->setInvitationLink($random_string);
            $event->setInvitationDateFrom($invitationDateFrom);
            $event->setInvitationDateTo($invitationDateTo);
            $event->setParticipationFields($jsonFields);
            $em->flush();

            if (is_null($link)) {
                $this->addFlash("success", "Der Einladungslink für " . $event->getName() . " wurde erzeugt.");
            } else {
                $this->addFlash("success", "Der Einladungslink für " . $event->getName() . " wurde geändert.");
            }
        } else {
            $this->addFlash("error", "Event mit der Id $id wurde nicht gefunden!");
        }

        return $this->redirectToRoute("showAllEvents");
    }

    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    /**
     * @Route("/events/show/participants/{id}", name="showParticipantsList")
     * @Security("is_granted('ROLE_elder')")
     * @param $id
     * @return Response
     */
    public function showParticipantsList($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($id);
        if ($event) {
            $participations = $em->getRepository(EventAttend::class)->findBy(array('eventId' => $event->getId()));
            $list = $this->FieldsToShowInTWIG($event->getParticipationFields());
            $NumberOfColumns = 2; // Id & DateTime
            foreach ($list as $entry) {
                if ($entry == "eat") {
                    $NumberOfColumns += 3;
                } else {
                    $NumberOfColumns++;
                }
            }


            return $this->render(
                'eventManagement/showParticipationList.html.twig',
                array(
                    'data' => $participations,
                    'showParticipationFields' => $list,
                    'NumberOfColumns' => $NumberOfColumns,
                )
            );
        } else {
            $this->addFlash("error", "Event mit der Id $id wurde nicht gefunden!");

            return $this->redirectToRoute("showAllEvents");
        }
    }

    /**
     * @param $eventFieldsArray
     * @return array
     */
    private function FieldsToShowInTWIG($eventFieldsArray)
    {
        $participationFields = json_decode($eventFieldsArray);
        $showParticipationFields = array();
        for ($i = 0; $i < count($participationFields); $i++) {
            if ($participationFields[$i][2]) {
                array_push($showParticipationFields, $participationFields[$i][0]);
            }
        }

        return $showParticipationFields;
    }

    /**
     * @Route("/events/attend/{invitationLink}", name="attendInvitationLink")
     * @param $invitationLink
     * @param Request $request
     * @param ReCaptchaService $reCaptcha
     * @return Response
     * @throws Exception
     */
    public function attendInvitationLink($invitationLink, Request $request, ReCaptchaService $reCaptcha)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->findOneBy(array('invitationLink' => $invitationLink));
        if ($event) {
            $loggedInUser_firstname = '';
            $loggedInUser_lastname = '';
            $loggedInUser_Stamm = '';
            if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                /** @var User $loggedInUser */
                $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();
                $loggedInUser_firstname = $loggedInUser->getFirstName();
                $loggedInUser_lastname = $loggedInUser->getLastName();
                $loggedInUser_Stamm = $loggedInUser->getStamm();
            }
            $now = new \DateTime();
            if (($event->getInvitationDateFrom() <= $now) && ($now <= $event->getInvitationDateTo())) {
                $valid_now = true;
            } else {
                $valid_now = false;
            }

            $eventAttend = new EventAttend();
            $eventAttend->setEventId($event->getId());
            $participationFields = json_decode($event->getParticipationFields());

            //$form = $this->createFormBuilder($eventAttend);
            $form = $this->createForm(
                EventAttendForm::class,
                $eventAttend,
                array(
                    "participationFields" => $participationFields,
                    "loggedInUser_firstname" => $loggedInUser_firstname,
                    "loggedInUser_lastname" => $loggedInUser_lastname,
                    "loggedInUser_Stamm" => $loggedInUser_Stamm,
                    "staemme" => json_decode($this->staemme, true),
                )
            );

            $form->handleRequest($request);

            /* handle submitted form */
            if ($form->isSubmitted() && $form->isValid() && $valid_now) {
                /* proof Google reCaptcha */
                if ($reCaptcha->validateReCaptcha(
                    $request->request->get("g-recaptcha-response")
                )) {
                    /* save input */
                    $eventAttend = $form->getData();

                    $em->persist($eventAttend);
                    $em->flush();

                    $this->addFlash("success", "Vielen Dank, Ihre Anmeldung ist bei uns erfolgreich eingegangen.");

                    return $this->redirectToRoute('login');
                }
                $this->addFlash("error", "Bestätige bitte den Spamschutz!");
            }

            return $this->render(
                'eventManagement/attendInvitationLink.html.twig',
                array(
                    "loggedInUser_firstname" => $loggedInUser_firstname,
                    "loggedInUser_lastname" => $loggedInUser_lastname,
                    "loggedInUser_Stamm" => $loggedInUser_Stamm,
                    "event" => $event,
                    "registrationAttendInvitationLink" => $form->createView(),
                    "showParticipationFields" => $this->FieldsToShowInTWIG($event->getParticipationFields()),
                    "valid_now" => $valid_now,
                )
            );
        } else {
            $this->addFlash("error", "Dieser Einladungslink ist leider nicht mehr gültig!");

            return $this->redirectToRoute("login");
        }
    }
}
