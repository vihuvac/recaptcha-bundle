<?php

/**
 * This file is part of the Recaptcha package.
 *
 * (c) Víctor Hugo Valle Castillo <victouk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

class TrueValidator extends ConstraintValidator
{
    /**
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_VERIFY_SERVER = "https://www.google.com/recaptcha/api/siteverify";

    /**
     * @var container
     */
    protected $container;


    /**
     * Construct.
     *
     * @param ContainerInterface $container An ContainerInterface instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // if recaptcha is disabled, always valid
        if (!$this->container->getParameter("vihuvac_recaptcha.enabled")) {
            return true;
        }

        // define variables for recaptcha check answer
        $secretKey = $this->container->getParameter("vihuvac_recaptcha.secret_key");
        $remoteip  = $this->container->get("request")->server->get("REMOTE_ADDR");
        $response  = $this->container->get("request")->get("g-recaptcha-response");

        if (!$this->checkAnswer($secretKey, $remoteip, $response)) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
      * Calls an HTTP POST function to verify if the user's guess was correct.
      *
      * @param string $secretKey
      * @param string $remoteip
      * @param string $response
      *
      * @throws ValidatorException When missing remote ip
      *
      * @return Boolean
      */
    private function checkAnswer($secretKey, $remoteip, $response)
    {
        if ($remoteip == null || $remoteip == "") {
            throw new ValidatorException("recaptcha_remote_ip");
        }

        // discard spam submissions
        if ($response == null || strlen($response) == 0) {
            return false;
        }


        $response = $this->getCurlRequest(self::RECAPTCHA_VERIFY_SERVER, $secretKey, $remoteip, $response);

        $answer = json_decode($response, true);

        if ($answer["success"] == true) {
            return true;
        }

        return false;
    }

    /**
     * Submit the cURL request with the specified parameters.
     *
     * @param string $url
     * @param string $secretKey
     * @param string $remoteip
     * @param string $response
     *
     * @return string Body of the reCAPTCHA response
     */
    private function getCurlRequest($url, $secretKey, $remoteip, $response)
    {
        $handle = curl_init($url);

        $options = array(
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $this->toQueryString($secretKey, $remoteip, $response),
            CURLOPT_HTTPHEADER     => array("Content-Type: application/x-www-form-urlencoded"),
            CURLINFO_HEADER_OUT    => false,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true
        );

        curl_setopt_array($handle, $options);

        $response = curl_exec($handle);

        curl_close($handle);

        return $response;
    }

    /**
     * Array representation.
     *
     * @return array Array formatted parameters.
     */
    private function toArray($secretKey, $remoteip, $response)
    {
        $params = array(
            "secret"   => $secretKey,
            "response" => $response
        );

        if (!is_null($remoteip)) {
            $params["remoteip"] = $remoteip;
        }

        return $params;
    }

    /**
     * Query string representation for HTTP request.
     *
     * @return string Query string formatted parameters.
     */
    private function toQueryString($secretKey, $remoteip, $response)
    {
        return http_build_query($this->toArray($secretKey, $remoteip, $response), "", "&");
    }
}
