<?php

namespace AppBundle\Controller\Feedback;

use AppBundle\Entity\UserFeedback;
use AppBundle\IpTools;
use DateTime;
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
        $feedbacktext = $data[0]["Text"];
        $feedbackSitePicureAsBase64 = $data[1];
        $feedbackUrlInfo = $data[2];
        $feedbackBrowserInfo = $data[3];
        $feedbackHtmlContent = $data[4];
        $feedbackTimestamp = $this->millisecTimstempToSecTimestemp($data[5]);
        $feedbackDate = new DateTime();
        $feedbackDate->setTimestamp($feedbackTimestamp);

        $href = $feedbackUrlInfo["href"];

        $userFeedback = new UserFeedback();
        $userFeedback->setText($feedbacktext);
        $userFeedback->setBrowserData($feedbackBrowserInfo);
        $userFeedback->setDate($feedbackDate);
        $userFeedback->setHtmlContent($feedbackHtmlContent);
        $userFeedback->setUrl($href);
        $userFeedback->setPicture(base64_decode($feedbackSitePicureAsBase64));
        $userFeedback->setUserIp(IpTools::getClientIp());

        if($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $loggedInUser = $this->get('security.token_storage')->getToken()->getUser();
            $userFeedback->setUserUid($loggedInUser->getUid());
            $userFeedback->setUserStamm($loggedInUser->getStamm());
            $userFeedback->setUserRoles(json_encode($loggedInUser->getRoles()));
        }

        $validator = $this->get('validator');
        $errors = $validator->validate($userFeedback);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString,500);
        }
        $em = $this->getDoctrine()->getManager();

        $em->persist($userFeedback);
        $em->flush();

        return new Response("",200);
    }

    private function millisecTimstempToSecTimestemp($millsecTimestep)
    {
        return intval($millsecTimestep/1000);
    }
}
