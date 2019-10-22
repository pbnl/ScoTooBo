<?php

namespace App\Controller\Web\Feedback;

use App\Entity\UserFeedback;
use App\IpTools;
use App\Model\Services\ReCaptchaService;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FeedbackController extends AbstractController
{

    /**
     * @Route("/feedback/send", name="sendFeedback")
     * @param Request $request
     * @param ReCaptchaService $reCaptcha
     * @param ValidatorInterface $validator
     * @return Response
     * @throws Exception
     */
    public function sendFeedback(Request $request, ReCaptchaService $reCaptcha, ValidatorInterface $validator)
    {
        $data = json_decode($request->get("data"), true);
        $feedbackText = htmlspecialchars($data[0]["Text"]);
        $feedbackSitePicureAsBase64 = htmlspecialchars($data[1]);
        $href = htmlspecialchars($data[2]["href"]);
        $feedbackBrowserInfo = htmlspecialchars($data[3]);
        $feedbackHtmlContent = htmlspecialchars($data[4]);
        $feedbackTimestamp = htmlspecialchars($this->millisecTimstempToSecTimestemp($data[5]));
        $feedbackDate = new DateTime();
        $feedbackDate->setTimestamp($feedbackTimestamp);
        $feedbackReCaptcha = htmlspecialchars($data[6]);

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

        $errors = $validator->validate($userFeedback);
        if (count($errors) > 0) {
            $errorsString = (string)$errors;

            return new Response($errorsString, 406);
        }

        if (!$reCaptcha->validateReCaptcha($feedbackReCaptcha)) {
            return new Response("Error with re-captcha", 403);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($userFeedback);
        $em->flush();

        return new Response("", 200);
    }

    private function millisecTimstempToSecTimestemp($millsecTimestep)
    {
        return intval($millsecTimestep / 1000);
    }

    /**
     * @Route("/feedback/show/all", name="showAllFeedback")
     * @Security("is_granted('ROLE_admin')")
     * @param Request $request
     * @return Response
     */
    public function showAllFeedback(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(UserFeedback::class);
        $userFeedbacks = $repository->findAll();

        return $this->render("admin/showAllFeedback.html.twig", array(
            "feedbacks" => $userFeedbacks,
        ));
    }
}
