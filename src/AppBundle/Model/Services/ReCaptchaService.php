<?php

namespace AppBundle\Model\Services;


class ReCaptchaService
{
    public function validateReCaptcha($feedbackReCaptcha, $reCaptchaSiteSecret)
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array('secret' => $reCaptchaSiteSecret, 'response' => $feedbackReCaptcha);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $jsonResult = json_decode($result, true);

        if ($jsonResult["success"] == "true") {
            return true;
        }
        return false;

    }
}