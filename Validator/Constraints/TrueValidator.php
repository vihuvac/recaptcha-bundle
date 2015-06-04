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
    const RECAPTCHA_VERIFY_SERVER = "www.google.com";

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

        // define variable for recaptcha check answer
        $secretKey = $this->container->getParameter("vihuvac_recaptcha.secret_key");

        $remoteip   = $this->container->get("request")->server->get("REMOTE_ADDR");
        $challenge  = $this->container->get("request")->get("recaptcha_challenge_field");
        $response   = $this->container->get("request")->get("recaptcha_response_field");

        if (!$this->checkAnswer($secretKey, $remoteip, $challenge, $response)) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
      * Calls an HTTP POST function to verify if the user's guess was correct
      *
      * @param string $secretKey
      * @param string $remoteip
      * @param string $challenge
      * @param string $response
      * @param array $extra_params an array of extra variables to post to the server
      *
      * @return ReCaptchaResponse
      */
    private function checkAnswer($secretKey, $remoteip, $challenge, $response, $extra_params = array())
    {
        if ($remoteip == null || $remoteip == "") {
            throw new ValidatorException("recaptcha_remote_ip");
        }

        // discard spam submissions
        if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
            return false;
        }

        $response = $this->httpPost(self::RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/verify", array(
            "secretkey" => $secretKey,
            "remoteip"  => $remoteip,
            "challenge" => $challenge,
            "response"  => $response
        ) + $extra_params);

        $answers = explode ("\n", $response [1]);

        if (trim($answers[0]) == "true") {
            return true;
        }

        return false;
    }

    /**
     * Submits an HTTP POST to a reCAPTCHA server
     *
     * @param string $host
     * @param string $path
     * @param array $data
     * @param int port
     *
     * @return array response
     */
    private function httpPost($host, $path, $data, $port = 80)
    {
        $req = $this->getQSEncode($data);

        $http_request  = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: ".strlen($req)."\r\n";
        $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $req;

        $response = null;
        if (!$fs = @fsockopen($host, $port, $errno, $errstr, 10)) {
            throw new ValidatorException("Could not open socket");
        }

        fwrite($fs, $http_request);

        while (!feof($fs)) {
            $response .= fgets($fs, 1160); // one TCP-IP packet
        }

        fclose($fs);

        $response = explode("\r\n\r\n", $response, 2);

        return $response;
    }

    /**
     * Encodes the given data into a query string format
     *
     * @param $data - array of string elements to be encoded
     *
     * @return string - encoded request
     */
    private function getQSEncode($data)
    {
        $req = null;
        foreach ($data as $key => $value) {
            $req .= $key."=".urlencode(stripslashes($value))."&";
        }

        // cut the last '&'
        $req = substr($req,0,strlen($req)-1);
        return $req;
    }
}
