<?php

namespace AppBundle\Controller\EventManagement;

use AppBundle\Entity\Event;
use AppBundle\Entity\EventAttend;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            "possibleFormFields"=>$this->getPossibleFormFields(),
        ]);
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
            array("vegi", "Vegetarier", false, false),
            array("comment", "Kommentar", true, false)
        );
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
                "attr" => [
                    "placeholder" => "Event.add.PriceInCent",
                    "min" => 0
                ],
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateInvitationLink($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($id);
        if ($event) {
            $FormFields = $this->getPossibleFormFields();
            /* read POST values */
            for ($i=0; $i<count($FormFields); $i++) {
                /* proofing if checkbox one is checked */
                $FormFields[$i][2]=false;
                if ($request->request->get($FormFields[$i][0]."_show")) {
                    $FormFields[$i][2] = true;
                }

                /* proofing if checkbox two is checked */
                $FormFields[$i][3]=false;
                if ($request->request->get($FormFields[$i][0]."_required") && $FormFields[$i][2]) {
                    $FormFields[$i][3] = true;
                }
            }
            $jsonFields = json_encode($FormFields);
            /*
            foreach ($FormFields as list($tag, $label, $show, $required)) {
                echo $tag."; ".$label."; ".$show."; ".$required."<br>";
            }
            echo $jsonFields;
            */



            $link = $event->getInvitationLink();

            $length = rand(
                $this->container->getParameter('events.invitationlink.length.min'),
                $this->container->getParameter('events.invitationlink.length.max')
            );
            $random_string = $this->generateRandomString($length);

            $event->setInvitationLink($random_string);
            $event->setParticipationFields($jsonFields);
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
     * @Route("/events/show/participants/{id}", name="showParticipantsList")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showParticipantsList($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($id);
        if ($event) {
            $participations = $em->getRepository(EventAttend::class)->findBy(array('eventId' => $event->getId()));

            return $this->render('eventManagement/showParticipationList.html.twig', array(
                'data' => $participations,
                'showParticipationFields' => $this->FieldsToShowInTWIG($event->getParticipationFields()),
            ));
        } else {
            $this->addFlash("error", "Event mit der Id $id wurde nicht gefunden!");
            return $this->redirectToRoute("showAllEvents");
        }
    }

    /**
     * @Route("/events/attend/{invitationLink}", name="attendInvitationLink")
     * @param $invitationLink
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function attendInvitationLink($invitationLink, Request $request)
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
            $participationFields = json_decode($event->getParticipationFields());

            $form = $this->createFormBuilder($eventAttend);
            for ($i=0; $i<count($participationFields); $i++) {
                switch ($participationFields[$i][0]) {
                    case "name":
                        if ($participationFields[$i][2]) {
                            $form->add(
                                'firstname',
                                TextType::class,
                                array(
                                    "attr" => ["placeholder" => "general.firstName"],
                                    'label' => "general.firstName",
                                    'empty_data' => '',
                                    'data' => $loggedInUser_Uid,
                                    "required" => $participationFields[$i][3]
                                )
                            )
                            ->add(
                                'lastname',
                                TextType::class,
                                array(
                                    "attr" => ["placeholder" => "general.lastName"],
                                    'label' => "general.lastName",
                                    'empty_data' => '',
                                    'data' => $loggedInUser_Uid,
                                    "required" => $participationFields[$i][3]
                                )
                            );
                        }
                        break;
                    case "email":
                        if ($participationFields[$i][2]) {
                            $form->add(
                                'email',
                                EmailType::class,
                                array(
                                    "attr" => ["placeholder" => "general.mail"],
                                    'label' => "general.mail",
                                    'empty_data' => '',
                                    "required" => $participationFields[$i][3]
                                )
                            );
                        }
                        break;
                    case "address":
                        if ($participationFields[$i][2]) {
                            $form->add(
                                'address_street',
                                TextType::class,
                                array(
                                    "attr" => ["placeholder" => "general.street"],
                                    'label' => "general.street",
                                    'empty_data' => '',
                                    "required" => $participationFields[$i][3]
                                )
                            )
                            ->add(
                                'address_nr',
                                TextType::class,
                                array(
                                    "attr" => ["placeholder" => "general.address_nr"],
                                    'label' => "general.address_nr",
                                    'empty_data' => '',
                                    "required" => $participationFields[$i][3]
                                )
                            )
                            ->add(
                                'address_plz',
                                IntegerType::class,
                                array(
                                    "attr" => ["placeholder" => "general.postalCode"],
                                    'label' => "general.postalCode",
                                    'empty_data' => '',
                                    "required" => $participationFields[$i][3]
                                )
                            )
                            ->add(
                                'address_city',
                                TextType::class,
                                array(
                                    "attr" => ["placeholder" => "general.place"],
                                    'label' => "general.place",
                                    'empty_data' => '',
                                    "required" => $participationFields[$i][3]
                                )
                            );
                        }
                        break;
                    case "stamm":
                        if ($participationFields[$i][2]) {
                            $form->add(
                                'stamm',
                                ChoiceType::class,
                                array(
                                    'label' => "general.stamm",
                                    'choices' => $this->container->getParameter('staemme'),
                                    'choice_label' => function ($value, $key, $index) {
                                        return $value;
                                    },
                                    'multiple' => false,
                                    'empty_data' => '',
                                    'data' => $loggedInUser_Stamm,
                                    "required" => $participationFields[$i][3]
                                )
                            );
                        }
                        break;
                    case "group":
                        if ($participationFields[$i][2]) {
                            $form->add(
                                'group',
                                TextType::class,
                                array(
                                    "attr" => ["placeholder" => "general.group"],
                                    'label' => "general.group",
                                    'empty_data' => '',
                                    "required" => $participationFields[$i][3]
                                )
                            );
                        }
                        break;
                    case "vegi":
                        if ($participationFields[$i][2]) {
                            $form->add(
                                'vegi',
                                ChoiceType::class,
                                array(
                                    'choices' => array(
                                        'general.yes' => true,
                                        'general.no' => false,
                                    ),
                                    'label' => 'general.vegi',
                                    'multiple' => false,
                                    'expanded' => true,
                                    "required" => $participationFields[$i][3]
                                )
                            );
                        }
                        break;
                    case "comment":
                        if ($participationFields[$i][2]) {
                            $form->add(
                                'comment',
                                TextareaType::class,
                                array(
                                    "attr" => ["placeholder" => "general.comment"],
                                    'label' => "general.comment",
                                    'empty_data' => '',
                                    "required" => $participationFields[$i][3]
                                )
                            );
                        }
                        break;
                }
            }
            $form = $form->add('save', SubmitType::class, array(
                    "label" => "Event.attendInvitationLink.submit",
                    "attr" => ["class" => "btn btn-lg btn-primary btn-block"]))
                ->getForm();

            $form->handleRequest($request);

            /* handle submitted form */
            if ($form->isSubmitted() && $form->isValid()) {
                /* proof Google reCaptcha */
                $reCaptchaSecret = $this->container->getParameter('recaptcha.secret');
                if ($this->get("reCaptcha")->validateReCaptcha($request->request->get("g-recaptcha-response"), $reCaptchaSecret)) {
                    /* save input */
                    $eventAttend = $form->getData();

                    $em->persist($eventAttend);
                    $em->flush();

                    $this->addFlash("success", "Vielen Dank, Ihre Anmeldung ist bei uns erfolgreich eingegangen.");

                    return $this->redirectToRoute('login');
                }
                $this->addFlash("error", "Bestätige bitte den Spamschutz!");
            }

            return $this->render('eventManagement/attendInvitationLink.html.twig', array(
                "loggedInUser_Uid"=>$loggedInUser_Uid,
                "loggedInUser_Stamm"=>$loggedInUser_Stamm,
                "event"=>$event,
                "registrationAttendInvitationLink"=>$form->createView(),
                "showParticipationFields"=>$this->FieldsToShowInTWIG($event->getParticipationFields()),
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

    /**
     * @param $eventFieldsArray
     * @return array
     */
    private function FieldsToShowInTWIG($eventFieldsArray) {
        $participationFields = json_decode($eventFieldsArray);
        $showParticipationFields = array();
        for ($i=0; $i<count($participationFields); $i++) {
            if ($participationFields[$i][2]) {
                array_push($showParticipationFields, $participationFields[$i][0]);
            }
        }

        return $showParticipationFields;
    }
}
