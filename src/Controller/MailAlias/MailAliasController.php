<?php

namespace App\Controller\MailAlias;

use App\Entity\LDAP\PbnlMailAlias;
use App\Model\Services\MailAliasRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type;

class MailAliasController extends AbstractController
{
    /**
     * @Route("/mailAlias/show/all", name="showAllMailAlias")
     * @Security("is_granted('ROLE_elder')")
     * @param Request $request
     * @param MailAliasRepository $mailAliasRepo
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \App\Model\Services\DatabaseObjectAllreadytExistsException
     */
    public function showMailAlias(Request $request, MailAliasRepository $mailAliasRepo)
    {
        $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();

        $allmailAlias = $mailAliasRepo->findAll();
        $allowdMailAlias = array();
        foreach ($allmailAlias as $mailAlias) {
            if ($this->isGranted("view", $mailAlias)) {
                array_push($allowdMailAlias, $mailAlias);
            }
        }

        $newMailAlias = new PbnlMailAlias();
        $newMailAlias->setForward([$loggedInUser->getMail()]);
        $addMailAliasForm = $this
            ->createFormBuilder($newMailAlias)
            ->add("mail", Type\EmailType::class, array(
                "attr" => ["placeholder" => "E-Mail Adresse"],
                'label' => "Mail",
                'empty_data' => '',
                "required" => true))
            ->add('submit', Type\SubmitType::class, array(
                "label" => "Hinzufügen",
                "attr" => ["class" => "btn btn-primary"]))
            ->getForm();

        $addMailAliasForm->handleRequest($request);
        if ($addMailAliasForm->isSubmitted() && $addMailAliasForm->isValid()) {
            $this->denyAccessUnlessGranted("add", $newMailAlias);
            $mailAlias = $addMailAliasForm->getData();
            $mailAliasRepo->add($mailAlias);
        }

        return $this->render('mailAliasManagment/showAllMailAlias.html.twig', [
            "mailAliasList" => $allowdMailAlias,
            "addMailAliasForm" => $addMailAliasForm->createView()
        ]);
    }


    /**
     * @Route("/mailAlias/detail", name="detailMailAlias")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailMailAlias(Request $request, MailAliasRepository $mailAliasRepo)
    {
        $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();

        $mailAlias = $mailAliasRepo->findByMail($request->get("mailAlias"));

        $this->denyAccessUnlessGranted("view", $mailAlias);

        $form = $this
            ->get("form.factory")
            ->createBuilder(Type\FormType::class, $mailAlias)
            ->add("forward", Type\CollectionType::class, [
                'entry_type'   => TextType::class,
                'label'        => 'Empfänger',
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'required'     => false,
                'attr'         => [
                    'class' => "mailAlias-collection",
                ]])
            ->add('submit', Type\SubmitType::class, array(
                "label" => "general.save",
                "attr" => ["class" => "btn btn-lg btn-primary btn-block"]))
            ->getForm();

        $oldMailAlias = new PbnlMailAlias();
        $oldMailAlias->setForward($mailAlias->getForward());
        $oldMailAlias->setMail($mailAlias->getMail());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted("edit", $oldMailAlias);
            $mailAlias = $form->getData();

            if (count($mailAlias->getForward()) > 0) {
                $mailAliasRepo->update($mailAlias);
            } else {
                $mailAliasRepo->remove($mailAlias);
                return $this->redirectToRoute("showAllMailAlias");
            }
        }

        return $this->render('mailAliasManagment/detailMailAlias.html.twig', [
            "mailAlias" => $mailAlias,
            "mailAliasForm" => $form->createView()
        ]);
    }
}
