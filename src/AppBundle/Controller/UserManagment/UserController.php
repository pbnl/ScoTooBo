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
     */
    public function showAllUser(Request $request)
    {
        //TODO Handle problem with corrupt users
        //Create search form
        $defaultData = array();
        $userSearchForm = $this->createFormBuilder($defaultData)
            ->add("filterOption",ChoiceType::class,array(
                "choices"=>array("username"=>"filterByUid","group"=>"filterByGroup"),
                'label'=>false,
                'required' => false,
                'data'=>"filterByUid"))
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

    /**
     * @Route("/users/add", name="addUser")
     * @Security("has_role('ROLE_elder')")
     */
    public function addUser(Request $request)
    {
        //Create the form
        $userRepo = $this->get("data.userRepository");
        $staemme = ["Ambronen","Hagen von Tronje","Anduril"];
        //TODO We need a better way to save or determine the names of the staemme!

        $user = new User("","","",[]);
        $addUserForm = $this->createFormBuilder($user,['attr' => ['class' => 'form-addUser']])
            ->add("firstName",TextType::class,array("attr"=>["placeholder"=>"firstName"],'label' => "firstName"))
            ->add("lastName",TextType::class,array("attr"=>["placeholder"=>"lastName"],'label' => "lastName"))
            ->add("givenName",TextType::class,array("attr"=>["placeholder"=>"username"],'label' => "username"))
            ->add("clearPassword",PasswordType::class,array("attr"=>["placeholder"=>"password"],'label' => "password"))
            ->add("generatePassword",ButtonType::class,array("attr"=>[],'label' => "addUser.generatePassword"))
            ->add("generatedPassword",TextType::class,array("attr"=>["readonly"=>"","placeholder"=>"addUser.generatedPassword"],"label"=>FALSE))
            ->add('stamm', ChoiceType::class, array(
                'choices'  => ArrayMethods::valueToKeyAndValue($staemme),
            ))
            ->add("send",SubmitType::class,array("label"=>"create","attr"=>["class"=>"btn btn-lg btn-primary btn-block"]))
            ->getForm();

        $addedSomeone = false;

        //Handel the form input
        $addUserForm->handleRequest($request);
        if($addUserForm->isSubmitted() && $addUserForm->isValid())
        {
            //Prepare User
            $user->setUid($user->getGivenName());

            //Create the new user
            try {
                $user = $userRepo->addUser($user);
                $this->addFlash("success", "Benutzer ".$user->getUid()." hinzugefügt");
                $addedSomeone = true;
            }
            catch (UserAlreadyExistException $e) {
                $this->addFlash("error", $e->getMessage());
            }
            catch (ContextErrorException $e) {
                $this->addFlash("error", $e->getMessage()." This probably means that this stamm (ou) does not exist.");
            }
        }

        //Render the page
        return $this->render("userManagment/addUser.html.twig",array(
            "addAUserForm" => $addUserForm->createView(),
            "addedPerson" => $user,
            "addedSomeone" => $addedSomeone
        ));
    }
}
