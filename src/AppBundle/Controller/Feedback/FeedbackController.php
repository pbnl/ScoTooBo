<?php

namespace AppBundle\Controller\Feedback;

use AppBundle\Entity\UserFeedback;
use AppBundle\IpTools;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends Controller
{

    /**
     * @Route("/feedback/send", name="sendFeedback")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendFeedback(Request $request)
    {
        $data = json_decode($request->get("data"), true);
        $feedbackText = htmlspecialchars($data[0]["Text"]);
        $feedbackSitePicureAsBase64 = htmlspecialchars($data[1]);
        $feedbackUrlInfo = htmlspecialchars($data[2]);
        $feedbackBrowserInfo = htmlspecialchars($data[3]);
        $feedbackHtmlContent = htmlspecialchars($data[4]);
        $feedbackTimestamp = htmlspecialchars($this->millisecTimstempToSecTimestemp($data[5]));
        $feedbackDate = new DateTime();
        $feedbackDate->setTimestamp($feedbackTimestamp);
        $feedbackReCaptcha = htmlspecialchars($data[6]);

        $href = $feedbackUrlInfo["href"];

        $userFeedback = new UserFeedback();
        $userFeedback->setText($feedbackText);
        $userFeedback->setBrowserData($feedbackBrowserInfo);
        $userFeedback->setDate($feedbackDate);
        $userFeedback->setHtmlContent($feedbackHtmlContent);
        $userFeedback->setUrl($href);
        $userFeedback->setPicture($feedbackSitePicureAsBase64);
        $userFeedback->setUserIp(IpTools::getClientIp());

        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();
            $userFeedback->setUserUid($loggedInUser->getUid());
            $userFeedback->setUserStamm($loggedInUser->getStamm());
            $userFeedback->setUserRoles(json_encode($loggedInUser->getRoles()));
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($userFeedback);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString, 406);
        }

        $reCaptchaSecret = $this->container->getParameter('recaptcha.secret');
        if (!$this->get("reCaptcha")->validateReCaptcha($feedbackReCaptcha, $reCaptchaSecret))
        {
             return new Response("Error with re-captcha", 403);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($userFeedback);
        $em->flush();

        return new Response("", 200);
    }

    private function millisecTimstempToSecTimestemp($millsecTimestep)
    {
        return intval($millsecTimestep/1000);
    }

    /**
     * @Route("/feedback/show/all", name="showAllFeedback")
     * @Security("has_role('ROLE_admin')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllFeedback(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(UserFeedback::class);
        $userFeedbacks = $repository->findAll();

        return $this->render("feedback/showAllFeedback.html.twig", array(
            "feedbacks"=> $userFeedbacks,
        ));
    }
}
