<?php

namespace AppBundle\Controller\MaterialManagement;

use AppBundle\Entity\Material;
use AppBundle\Entity\MaterialOffers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\MaterialOfferType;

class MaterialController extends Controller
{
    /**
     * @Route("/material/show/all", name="showAllMaterial")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllMaterial(Request $request)
    {
        return $this->render('materialManagement/showAllMaterial.html.twig', [
#            "events"=>$events,
        ]);
    }

    /**
     * @Route("/material/add", name="addMaterial")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMaterial(Request $request)
    {
        $material = new Material();

        $offer1 = new MaterialOffers();
        $offer2 = new MaterialOffers();

        $material->getOffers()->add($offer1);
        $material->getOffers()->add($offer2);

        $addMaterialForm = $this->createFormBuilder($material)
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
            ->add('offers', CollectionType::class, array(
                'entry_type' => MaterialOfferType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__prototype__',
                'by_reference' => false,
                'error_bubbling' => false))
            ->add('save', SubmitType::class, array(
                'label' => 'Event.add.Submit',
                "attr"=>["class"=>"btn btn-primary"]))
            ->getForm();

        $addMaterialForm->handleRequest($request);

        if ($addMaterialForm->isSubmitted() && $addMaterialForm->isValid()) {
            #$event_data = $addAnEventForm->getData();
            #$em = $this->getDoctrine()->getManager();
            #$em->persist($event_data); // tells Doctrine you want to (eventually) save the Product (no queries yet)
            #$em->flush(); // actually executes the queries (i.e. the INSERT query)
            #$this->addFlash("success", "Event wurde mit der Id ".$event_data->getId()." erstellt.");
            #return $this->redirectToRoute('showAllEvents');
        }
        return $this->render('materialManagement/addMaterial.html.twig', array(
            'addMaterialForm' => $addMaterialForm->createView(),
        ));
        #$this->addFlash("info", "This function is comming soon!");
        #return $this->redirectToRoute("showAllMaterial");
    }

    /**
     * @Route("/material/detail", name="detailMaterial")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailMaterial(Request $request)
    {
        $this->addFlash("info", "This function is comming soon!");
        return $this->redirectToRoute("showAllMaterial");
    }

    /**
     * @Route("/material/remove", name="removeMaterial")
     * @Security("has_role('ROLE_elder')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeMaterial(Request $request)
    {
        $this->addFlash("info", "This function is comming soon!");
        return $this->redirectToRoute("showAllMaterial");
    }
}
