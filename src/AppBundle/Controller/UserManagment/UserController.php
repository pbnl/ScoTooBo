<?php

namespace AppBundle\Controller\UserManagment;

use AppBundle\ArrayMethods;
use AppBundle\Model\Filter;
use AppBundle\Model\Services\GroupNotFoundException;
use AppBundle\Model\Services\UserAlreadyExistException;
use AppBundle\Model\Services\UserDoesNotExistException;
use AppBundle\Model\Services\UserNotUniqueException;
use AppBundle\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Route("/users/show/all", name="showAllUser")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllUser(Request $request)
    {
        //TODO Handle problem with corrupt users
        //Create search form
        $defaultData = array();
        $userSearchForm = $this->createFormBuilder($defaultData)
            ->add("filterOption", ChoiceType::class, array(
                "choices"=>array("general.username"=>"filterByUid", "general.group"=>"filterByGroup"),
                'label'=>false,
                'required' => false,
                'data'=>"filterByUid"))
            ->add("filterText", TextType::class, array(
                "attr"=>["placeholder"=>"general.search"],
                'label'=>false,
                'required' => false))
            ->add("send", SubmitType::class, array(
                "label"=>"general.search",
                "attr"=>["class"=>"btn btn-primary"]))
            ->setMethod("get")
            ->getForm();

        $filter = new Filter();

        //Handel the form input
        $userSearchForm->handleRequest($request);
        if ($userSearchForm->isSubmitted() && $userSearchForm->isValid()) {
            $data = $userSearchForm->getData();
            $filter->addFilter($data["filterOption"], $data["filterText"]);
        }

        $userRepo = $this->get("data.userRepository");
        try {
            $users = $userRepo->findAllUsersByComplexFilter($filter);
        } catch (GroupNotFoundException $e) {
            $users = [];
            $this->addFlash("info", $e->getMessage());
        }

        return $this->render('userManagement/showAllUsers.html.twig', [
            "peopleSearchForm" => $userSearchForm->createView(),
            "users"=>$users,
        ]);
    }

    /**
     * @Route("/users/add", name="addUser")
     * @Security("has_role('ROLE_stavo')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addUser(Request $request)
    {
        //Create the form
        $userRepo = $this->get("data.userRepository");
        $staemme = ["Ambronen","Hagen von Tronje","Anduril"];
        //TODO We need a better way to save or determine the names of the staemme!

        $user = new User("", "", "", []);
        $addUserForm = $this->createFormBuilder($user, ['attr' => ['class' => 'form-addUser']])
            ->add("firstName", TextType::class, array(
                "attr"=>["placeholder"=>"general.firstName"],
                'label' => "general.firstName"))
            ->add("lastName", TextType::class, array(
                "attr"=>["placeholder"=>"general.lastName"],
                'label' => "general.lastName"))
            ->add("givenName", TextType::class, array(
                "attr"=>["placeholder"=>"general.username"],
                'label' => "general.username"))
            ->add("clearPassword", PasswordType::class, array(
                "attr"=>["placeholder"=>"general.password"],
                'label' => "general.password"))
            ->add("generatePassword", ButtonType::class, array(
                "attr"=>[],
                'label' => "User.add.generatePassword"))
            ->add("generatedPassword", TextType::class, array(
                "attr"=>["readonly"=>"",
                    "placeholder"=>"User.add.generatedPassword"],
                "label"=>false))
            ->add('stamm', ChoiceType::class, array(
                'choices'  => ArrayMethods::valueToKeyAndValue($staemme),
                'label' => "general.stamm"
            ))
            ->add('wikiAcces', CheckboxType::class, array(
                'mapped' => false,
                "attr"=>["placeholder"=>"User.add.wikiAccess"],
                'label' => "User.add.wikiAccess",
                'required' => false
            ))
            ->add("send", SubmitType::class, array(
                "label"=>"general.create",
                "attr"=>["class"=>"btn btn-lg btn-primary btn-block"]))
            ->getForm();

        $addedSomeone = false;

        //Handel the form input
        $addUserForm->handleRequest($request);
        if ($addUserForm->isSubmitted() && $addUserForm->isValid()) {
            //Prepare User
            $user->setUid($user->getGivenName());

            //Create the new user
            try {
                $user = $userRepo->addUser($user);

                //We have to load the user to get the correct dn
                $user = $userRepo->getUserByUid($user->getUid());

                $groupRepo = $this->get("data.groupRepository");
                $nordlichterGroup = $groupRepo->findByCn("nordlichter");
                $nordlichterGroup->addUser($user);
                $groupRepo->updateGroup($nordlichterGroup);

                if ($addUserForm->get("wikiAcces")->getData()) {
                    $wikiGroup = $groupRepo->findByCn("wiki");
                    $wikiGroup->addUser($user);
                    $groupRepo->updateGroup($wikiGroup);
                }

                $this->addFlash("success", "Benutzer ".$user->getUid()." hinzugefügt");
                $addedSomeone = true;
            } catch (UserAlreadyExistException $e) {
                $this->addFlash("error", $e->getMessage());
            } catch (ContextErrorException $e) {
                $this->addFlash("error", $e->getMessage()." Das bedeutet wahrscheinlich, dass der Stamm (ou) nicht existiert");
            }
        }

        //Render the page
        return $this->render("userManagement/addUser.html.twig", array(
            "addAUserForm" => $addUserForm->createView(),
            "addedPerson" => $user,
            "addedSomeone" => $addedSomeone
        ));
    }

    /**
     * @Route("/users/detail", name="detailUser")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetailUser(Request $request)
    {
        $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();
        $userRepo = $this->get("data.userRepository");

        if ($request->get("uid", $loggedInUser->getUid()) != $loggedInUser->getUid()) {
            $this->denyAccessUnlessGranted("ROLE_elder", null, "You are not allowed to see this user");

            $userToShow = $userRepo->getUserByUid($request->get("uid"));
        } else {
            $userToShow = $loggedInUser;
        }

        $editUserForm = false;
        //is the user allowed to edit this user?
        //TODO add the ability for groupleaders to edit their users
        if ($this->isLoggedInUserAllowedToEditShowenUser($loggedInUser, $userToShow)) {
            $editUserForm = $this->createFormBuilder($userToShow, ['attr' => ['class' => 'form-addAUser']])
                ->add("firstName", TextType::class, array(
                    "attr" => ["placeholder" => "general.firstName"],
                    'label' => "general.firstName",
                    'empty_data' => '',
                    "required" => true))
                ->add("lastName", TextType::class, array(
                    "attr" => ["placeholder" => "general.lastName"],
                    'label' => "general.lastName",
                    'empty_data' => '',
                    "required" => true))
                ->add("city", TextType::class, array(
                    "attr" => ["placeholder" => "general.city"],
                    'label' => "general.city",
                    'empty_data' => '',
                    "required" => false))
                ->add("postalCode", TextType::class, array(
                    "attr" => ["placeholder" => "general.postalCode"],
                    'label' => "general.postalCode",
                    'empty_data' => '',
                    "required" => false))
                ->add("street", TextType::class, array(
                    "attr" => ["placeholder" => "general.street"],
                    'label' => "general.street",
                    'empty_data' => '',
                    "required" => false))
                ->add("homePhoneNumber", TextType::class, array(
                    "attr" => ["placeholder" => "general.phoneNumber.home"],
                    'label' => "general.phoneNumber.home",
                    'empty_data' => '',
                    "required" => false))
                ->add("mobilePhoneNumber", TextType::class, array(
                    "attr" => ["placeholder" => "general.phoneNumber.mobile"],
                    'label' => "general.phoneNumber.mobil",
                    'empty_data' => '',
                    "required" => false))
                ->add("send", SubmitType::class, array(
                    "label" => "general.save",
                    "attr" => ["class" => "btn btn-lg btn-primary btn-block"]))
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
        return $this->render("userManagement/detailUser.html.twig", array(
            "user"=>$userToShow,
            "editUserForm" => $editUserForm,
        ));
    }

    private function isLoggedInUserAllowedToEditShowenUser($loggedInUser, $userToShow)
    {
        if ($userToShow->getUid() == $loggedInUser->getUid()){
            return true;
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_EDIT_ALL_USERS')) {
            return true;
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_stavo')
            && $userToShow->getStamm() == $loggedInUser->getStamm()) {
            return true;
        }
        return false;
    }

    /**
     * @Route("/users/remove", name="removeUser")
     * @param Request $request
     * @return Response
     */
    public function removeUser(Request $request)
    {
        $uid = $request->get("uid", "");
        $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();

        try {
            $userRepo = $this->get("data.userRepository");
            $userToRemove = $userRepo->getUserByUid($uid);

            if ($this->isLoggedInUserAllowedToDeleteUser($loggedInUser, $userToRemove)) {
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

    private function isLoggedInUserAllowedToDeleteUser($loggedInUser, $userToRemove)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_REMOVE_ALL_USERS')) {
            return true;
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_stavo')
            && $userToRemove->getStamm() == $loggedInUser->getStamm()) {
            return true;
        }
        return false;
    }
}
