<?php

namespace App\Controller\UserManagment;

use App\ArrayMethods;
use App\Model\Filter;
use App\Model\Services\GroupNotFoundException;
use App\Model\Services\GroupRepository;
use App\Model\Services\UserAlreadyExistException;
use App\Model\Services\UserDoesNotExistException;
use App\Model\Services\UserNotUniqueException;
use App\Model\Services\UserRepository;
use App\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/users/show/all", name="showAllUser")
     * @Security("is_granted('ROLE_SHOW_ALL_USERS')")
     * @param Request $request
     * @param UserRepository $userRepo
     * @return Response
     */
    public function showAllUser(Request $request, UserRepository $userRepo):Response
    {
        //TODO Handle problem with corrupt users
        //Create search form
        $defaultData = array();
        $userSearchForm = $this->createFormBuilder($defaultData)
            ->add(
                "filterOption",
                ChoiceType::class,
                array(
                    "choices" => array("general.username" => "filterByUid", "general.group" => "filterByGroup"),
                    'label' => false,
                    'required' => false,
                    'data' => "filterByUid",
                )
            )
            ->add(
                "filterText",
                TextType::class,
                array(
                    "attr" => ["placeholder" => "general.search"],
                    'label' => false,
                    'required' => false,
                )
            )
            ->add(
                "send",
                SubmitType::class,
                array(
                    "label" => "general.search",
                    "attr" => ["class" => "btn btn-primary"],
                )
            )
            ->setMethod("get")
            ->getForm();

        $filter = new Filter();

        //Handel the form input
        $userSearchForm->handleRequest($request);
        if ($userSearchForm->isSubmitted() && $userSearchForm->isValid()) {
            $data = $userSearchForm->getData();
            $filter->addFilter($data["filterOption"], $data["filterText"]);
        }

        try {
            $users = $userRepo->findAllUsersByComplexFilter($filter);
        } catch (GroupNotFoundException $e) {
            $users = [];
            $this->addFlash("info", $e->getMessage());
        }

        return $this->render(
            'userManagement/showAllUsers.html.twig',
            [
                "peopleSearchForm" => $userSearchForm->createView(),
                "users" => $users,
            ]
        );
    }

    /**
     * @Route("/users/add", name="addUser")
     * @Security("is_granted('ROLE_stavo')")
     * @param Request $request
     * @param UserRepository $userRepo
     * @param GroupRepository $groupRepo
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function addUser(Request $request, UserRepository $userRepo, GroupRepository $groupRepo, TranslatorInterface $translator):Response
    {
        //Create the form
        $jsonStaemme = $this->getParameter('staemme');
        $staemme = json_decode($jsonStaemme, true);
        //TODO We need a better way to save or determine the names of the staemme!

        $user = new User("", "", "", []);
        $addUserForm = $this->createFormBuilder($user, ['attr' => ['class' => 'form-addUser']])
            ->add(
                "firstName",
                TextType::class,
                array(
                    "attr" => ["placeholder" => "general.firstName"],
                    'label' => "general.firstName",
                )
            )
            ->add(
                "lastName",
                TextType::class,
                array(
                    "attr" => ["placeholder" => "general.lastName"],
                    'label' => "general.lastName",
                )
            )
            ->add(
                "givenName",
                TextType::class,
                array(
                    "attr" => ["placeholder" => "general.username"],
                    'label' => "general.username",
                )
            )
            ->add(
                "clearPassword",
                PasswordType::class,
                array(
                    "attr" => ["placeholder" => "general.password"],
                    'label' => "general.password",
                )
            )
            ->add(
                "generatePassword",
                ButtonType::class,
                array(
                    "attr" => [],
                    'label' => "User.add.generatePassword",
                )
            )
            ->add(
                "generatedPassword",
                TextType::class,
                array(
                    "attr" => [
                        "readonly" => "",
                        "placeholder" => "User.add.generatedPassword",
                    ],
                    "label" => false,
                )
            )
            ->add(
                'stamm',
                ChoiceType::class,
                array(
                    'choices' => ArrayMethods::valueToKeyAndValue($staemme),
                    'label' => "general.stamm",
                    "attr" => [
                        "data-step" => "4",
                        "data-intro" => $translator->trans('IntroJS.addUser.stamm'),
                    ],
                )
            )
            ->add(
                'wikiAcces',
                CheckboxType::class,
                array(
                    'mapped' => false,
                    "attr" => ["placeholder" => "User.add.wikiAccess"],
                    'label' => "User.add.wikiAccess",
                    'required' => false,
                )
            )
            ->add(
                'elderRole',
                CheckboxType::class,
                array(
                    'mapped' => false,
                    "attr" => [
                        "placeholder" => "User.add.eldeRole",
                        "data-step" => "3",
                        "data-intro" => $translator->trans('IntroJS.addUser.wikiAcces'),
                    ],
                    'label' => "User.add.eldeRole",
                    'required' => false,
                )
            )
            ->add(
                'sendInvitationMail',
                CheckboxType::class,
                array(
                    'mapped' => false,
                    "attr" => [
                        "placeholder" => "User.add.sendInvitationMail",
                        "data-intro" => $translator->trans('IntroJS.addUser.wikiAcces'),
                    ],
                    'label' => "Einladungsmail verschicken",
                    'required' => false,
                )
            )
            ->add(
                "sendInvitationMailAddress",
                TextType::class,
                array(
                    'mapped' => false,
                    "attr" => ["placeholder" => "Mailadresse für Einladungsmail", "disabled"=>""],
                    'label' => "Mailadresse für Einladungsmail",
                )
            )
            ->add(
                "send",
                SubmitType::class,
                array(
                    "label" => "general.create",
                    "attr" => [
                        "class" => "btn btn-lg btn-primary btn-block",
                        "data-step" => "5",
                        "data-intro" => $translator->trans('IntroJS.addUser.submit'),
                    ],
                )
            )
            ->getForm();

        $addedSomeone = false;

        //Handel the form input
        $addUserForm->handleRequest($request);
        if ($addUserForm->isSubmitted() && $addUserForm->isValid()) {
            //Prepare User
            $user->setUid($user->getGivenName());
            $user->setMail($user->getUid()."@pbnl.de");

            //Create the new user
            try {
                $user = $userRepo->addUser($user);

                //We have to load the user to get the correct dn
                $user = $userRepo->getUserByUid($user->getUid());

                $nordlichterGroup = $groupRepo->findByCn("nordlichter");
                $nordlichterGroup->addUser($user);
                $groupRepo->updateGroup($nordlichterGroup);

                if ($addUserForm->get("wikiAcces")->getData()) {
                    $wikiGroup = $groupRepo->findByCn("wiki");
                    $wikiGroup->addUser($user);
                    $groupRepo->updateGroup($wikiGroup);
                }
                if ($addUserForm->get("elderRole")->getData()) {
                    $elderGroup = $groupRepo->findByCn("elder");
                    $elderGroup->addUser($user);
                    $groupRepo->updateGroup($elderGroup);
                }
                if ($addUserForm->get("sendInvitationMail")->getData()) {
                    $this->sendInventationMail($addUserForm, $this->get('swiftmailer.mailer'));
                }

                $this->addFlash("success", "Benutzer ".$user->getUid()." hinzugefügt");
                $addedSomeone = true;
            } catch (UserAlreadyExistException $e) {
                $this->addFlash("error", $e->getMessage());
            }/* catch (ContextErrorException $e) {
                $this->addFlash(
                    "error",
                    $e->getMessage()." Das bedeutet wahrscheinlich, dass der Stamm (ou) nicht existiert"
                );
            }*/
        }

        //Render the page
        return $this->render(
            "userManagement/addUser.html.twig",
            array(
                "addAUserForm" => $addUserForm->createView(),
                "addedPerson" => $user,
                "addedSomeone" => $addedSomeone,
            )
        );
    }

    /**
     * @Route("/users/detail", name="detailUser")
     * @param Request $request
     * @param UserRepository $userRepo
     * @return Response
     */
    public function showDetailUser(Request $request, UserRepository $userRepo):Response
    {
        $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();

        if ($request->get("uid", $loggedInUser->getUid()) != $loggedInUser->getUid()) {
            $userToShow = $userRepo->getUserByUid($request->get("uid"));
        } else {
            $userToShow = $loggedInUser;
        }

        $this->denyAccessUnlessGranted("view", $userToShow, "You are not allowed to see this user");

        $editUserForm = false;
        //is the user allowed to edit this user?
        //TODO add the ability for groupleaders to edit their users
        if ($this->isGranted("edit", $userToShow)) {
            $editUserForm = $this->createFormBuilder($userToShow, ['attr' => ['class' => 'form-addAUser']])
                ->add(
                    "firstName",
                    TextType::class,
                    array(
                        "attr" => ["placeholder" => "general.firstName"],
                        'label' => "general.firstName",
                        'empty_data' => '',
                        "required" => true,
                    )
                )
                ->add(
                    "lastName",
                    TextType::class,
                    array(
                        "attr" => ["placeholder" => "general.lastName"],
                        'label' => "general.lastName",
                        'empty_data' => '',
                        "required" => true,
                    )
                )
                ->add(
                    "city",
                    TextType::class,
                    array(
                        "attr" => ["placeholder" => "general.city"],
                        'label' => "general.city",
                        'empty_data' => '',
                        "required" => false,
                    )
                )
                ->add(
                    "postalCode",
                    TextType::class,
                    array(
                        "attr" => ["placeholder" => "general.postalCode"],
                        'label' => "general.postalCode",
                        'empty_data' => '',
                        "required" => false,
                    )
                )
                ->add(
                    "street",
                    TextType::class,
                    array(
                        "attr" => ["placeholder" => "general.street"],
                        'label' => "general.street",
                        'empty_data' => '',
                        "required" => false,
                    )
                )
                ->add(
                    "homePhoneNumber",
                    TextType::class,
                    array(
                        "attr" => ["placeholder" => "general.phoneNumber.home"],
                        'label' => "general.phoneNumber.home",
                        'empty_data' => '',
                        "required" => false,
                    )
                )
                ->add(
                    "mobilePhoneNumber",
                    TextType::class,
                    array(
                        "attr" => ["placeholder" => "general.phoneNumber.mobile"],
                        'label' => "general.phoneNumber.mobil",
                        'empty_data' => '',
                        "required" => false,
                    )
                )
                ->add(
                    "send",
                    SubmitType::class,
                    array(
                        "label" => "general.save",
                        "attr" => ["class" => "btn btn-lg btn-primary btn-block"],
                    )
                )
                ->getForm();

            //Handel the form input
            $editUserForm->handleRequest($request);
            if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
                $userRepo->updateUser($userToShow);
                $this->addFlash("success", "Änderungen gespeichert");
            } elseif ($editUserForm->isSubmitted() && !$editUserForm->isValid()) {
                $this->addFlash("error", "Falsche Werte!");
                $userToShow = $userRepo->getUserByUid($request->get("uid", $loggedInUser->getUid()));
            }

            $editUserForm = $editUserForm->createView();
        }


        //Render the page
        return $this->render(
            "userManagement/detailUser.html.twig",
            array(
                "user" => $userToShow,
                "editUserForm" => $editUserForm,
            )
        );
    }

    /**
     * @Route("/users/remove", name="removeUser")
     * @param Request $request
     * @param UserRepository $userRepo
     * @return Response
     */
    public function removeUser(Request $request, UserRepository $userRepo):Response
    {
        $uid = $request->get("uid", "");

        try {
            $userToRemove = $userRepo->getUserByUid($uid);

            if ($this->isGranted("remove", $userToRemove)) {
                $userRepo->removeUser($userToRemove);

                $this->addFlash("success", $uid." wurde gelöscht");
            } else {
                throw $this->createAccessDeniedException("You are not allowed to remove the user $uid");
            }


        } catch (UserDoesNotExistException $e) {
            $this->addFlash("error", "Der User $uid existiert nicht!");
        } catch (UserNotUniqueException $e) {
            $this->addFlash("error", "Der User $uid ist nicht einzigartig!");
        }

        return $this->redirectToRoute("showAllUser");
    }

    private function sendInventationMail(\Symfony\Component\Form\FormInterface $addUserForm, \Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Dein PBNL Account ' . $addUserForm->get("givenName")->getData()))
            ->setFrom('scotoobo@pbnl.de')
            ->setTo($addUserForm->get("sendInvitationMailAddress")->getData())
            ->setBody(
                $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'Emails/invitation.html.twig',
                    array(
                        'firstName' => $addUserForm->get("firstName")->getData(),
                        'lastName' => $addUserForm->get("lastName")->getData(),
                        'password' => $addUserForm->get("clearPassword")->getData(),
                        'givenName' => $addUserForm->get("givenName")->getData())
                ),
                'text/html'
            );

        $mailer->send($message);

    }
}
