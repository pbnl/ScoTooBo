<?php

namespace AppBundle\Controller\UserManagment;

use AppBundle\ArrayMethods;
use AppBundle\Model\Filter;
use AppBundle\Model\Services\GroupNotFoundException;
use AppBundle\Model\Services\UserAlreadyExistException;
use AppBundle\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

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
                "choices"=>array("username"=>"filterByUid", "group"=>"filterByGroup"),
                'label'=>false,
                'required' => false,
                'data'=>"filterByUid"))
            ->add("filterText", TextType::class, array(
                "attr"=>["placeholder"=>"search"],
                'label'=>false,
                'required' => false))
            ->add("send", SubmitType::class, array(
                "label"=>"search",
                "attr"=>["class"=>"btn btn-lg btn-primary btn-block"]))
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
            $users = $userRepo->getAllUsers($filter);
        } catch (GroupNotFoundException $e) {
            $users = [];
            $this->addFlash("info", $e->getMessage());
        }

        return $this->render('userManagment/showAllUsers.html.twig', [
            "peopleSearchForm" => $userSearchForm->createView(),
            "users"=>$users,
        ]);
    }

    /**
     * @Route("/users/add", name="addUser")
     * @Security("has_role('ROLE_elder')")
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
                "attr"=>["placeholder"=>"firstName"],
                'label' => "firstName"))
            ->add("lastName", TextType::class, array(
                "attr"=>["placeholder"=>"lastName"],
                'label' => "lastName"))
            ->add("givenName", TextType::class, array(
                "attr"=>["placeholder"=>"username"],
                'label' => "username"))
            ->add("clearPassword", PasswordType::class, array(
                "attr"=>["placeholder"=>"password"],
                'label' => "password"))
            ->add("generatePassword", ButtonType::class, array(
                "attr"=>[],
                'label' => "addUser.generatePassword"))
            ->add("generatedPassword", TextType::class, array(
                "attr"=>["readonly"=>"",
                    "placeholder"=>"addUser.generatedPassword"],
                "label"=>false))
            ->add('stamm', ChoiceType::class, array(
                'choices'  => ArrayMethods::valueToKeyAndValue($staemme),
            ))
            ->add("send", SubmitType::class, array(
                "label"=>"create",
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
                $this->addFlash("success", "Benutzer ".$user->getUid()." hinzugefügt");
                $addedSomeone = true;
            } catch (UserAlreadyExistException $e) {
                $this->addFlash("error", $e->getMessage());
            } catch (ContextErrorException $e) {
                $this->addFlash("error", $e->getMessage()." This probably means that this stamm (ou) does not exist.");
            }
        }

        //Render the page
        return $this->render("userManagment/addUser.html.twig", array(
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

        if ($request->get("uid", "") != "" && $request->get("uid", "") != $loggedInUser) {
            $this->denyAccessUnlessGranted("ROLE_elder", null, "You are not allowed to edit this user");
            //TODO maybe add a better security restriction

            $userToShow = $userRepo->getUserByUid($request->get("uid"));
        } else {
            $userToShow = $loggedInUser;
        }

        $editUserForm = false;
        //is the user allowed to edit this user?
        //TODO maybe add a better security restriction
        if ($userToShow->getUid() == $loggedInUser->getUid()) {
            $editUserForm = $this->createFormBuilder($userToShow, ['attr' => ['class' => 'form-addAUser']])
                ->add("firstName", TextType::class, array(
                    "attr" => ["placeholder" => "firstName"],
                    'label' => "firstName",
                    "required" => true))
                ->add("lastName", TextType::class, array(
                    "attr" => ["placeholder" => "lastName"],
                    'label' => "lastName",
                    "required" => true))
                ->add("city", TextType::class,array(
                    "attr" => ["placeholder" => "city"],
                    'label' => "city",
                    "required" => false))
                ->add("postalCode", TextType::class,array(
                    "attr" => ["placeholder" => "postalCode"],
                    'label' => "postalCode",
                    "required" => false))
                ->add("street", TextType::class,array(
                    "attr" => ["placeholder" => "street"],
                    'label' => "street",
                    "required" => false))
                ->add("homePhoneNumber", TextType::class,array(
                    "attr" => ["placeholder" => "phoneNumber.home"],
                    'label' => "phoneNumber.home",
                    "required" => false))
                ->add("mobilePhoneNumber", TextType::class,array(
                    "attr" => ["placeholder" => "phoneNumber.mobile"],
                    'label' => "phoneNumber.mobil",
                    "required" => false))
                ->add("send", SubmitType::class, array(
                    "label" => "save",
                    "attr" => ["class" => "btn btn-lg btn-primary btn-block"]))
                ->getForm();

            //Handel the form input
            $editUserForm->handleRequest($request);
            if($editUserForm->isSubmitted() && $editUserForm->isValid()) {
                $userRepo->updateUser($userToShow);
                $this->addFlash("success", "Änderungen gespeichert");
            } elseif ($editUserForm->isSubmitted() && !$editUserForm->isValid()) {
                $this->addFlash("error", "Falsche Werte!");
                $userToShow = $userRepo->getUserByUid($request->get("uid", $loggedInUser->getUid()));
            }

            $editUserForm = $editUserForm->createView();
        }


        //Render the page
        return $this->render("userManagment/detailUser.html.twig", array(
            "user"=>$userToShow,
            "editUserForm" => $editUserForm,
        ));
    }
}
