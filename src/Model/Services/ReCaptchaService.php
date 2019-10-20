<?php

namespace App\Model\Services;

class ReCaptchaService
{
    public function __construct($recaptcha)
    {
        $this->reCaptchaSiteSecret = $recaptcha["secret"];
        $this->bypass_real_validaion = (boolean)json_decode(strtolower($recaptcha["testing.bypass"]));
        $this->bypass_validation_result_is_allow = (boolean)json_decode(strtolower($recaptcha["testing.bypass_allow"]));
    }

    public function validateReCaptcha($feedbackReCaptchaResponse)
    {
        if ($this->bypass_real_validaion) {
            return $this->bypass_validation_result_is_allow;
        }
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array('secret' => $this->reCaptchaSiteSecret, 'response' => $feedbackReCaptchaResponse);

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
