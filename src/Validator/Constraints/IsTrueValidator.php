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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

class IsTrueValidator extends ConstraintValidator
{
    /**
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_VERIFY_SERVER = "https://www.google.com";

    /**
     * Enable reCaptcha
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * Recaptcha Private Key
     *
     * @var Boolean
     */
    protected $secretKey;

    /**
     * Request Stack
     *
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * HTTP Proxy informations
     * @var Array
     */
    protected $httpProxy;

    /**
     * Enable serverside host check.
     *
     * @var Boolean
     */
    protected $verifyHost;


    /**
     * Construct.
     *
     * @param Boolean      $enabled
     * @param String       $secretKey
     * @param RequestStack $requestStack
     * @param Array        $httpProxy
     * @param Boolean      $verifyHost
     */
    public function __construct($enabled, $secretKey, RequestStack $requestStack, array $httpProxy, $verifyHost)
    {
        $this->enabled      = $enabled;
        $this->secretKey    = $secretKey;
        $this->requestStack = $requestStack;
        $this->httpProxy    = $httpProxy;
        $this->verifyHost   = $verifyHost;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // If the reCAPTCHA is disabled, it's always valid.
        if (!$this->enabled) {
            return true;
        }

        // Define the variable for reCAPTCHA check answer.
        $masterRequest = $this->requestStack->getMasterRequest();
        $remoteip      = $masterRequest->getClientIp();
        $response      = $masterRequest->get("g-recaptcha-response");

        $isValid = $this->checkAnswer($this->secretKey, $remoteip, $response);

        if ($isValid["success"] !== true) {
            $this->context->addViolation($constraint->message);
        // Perform server side hostname check
        } elseif ($this->verifyHost && $isValid["hostname"] !== $masterRequest->getHost()) {
            $this->context->addViolation($constraint->invalidHostMessage);
        }
    }

    /**
      * Calls an HTTP POST function to verify if the user's guess was correct.
      *
      * @param String $secretKey
      * @param String $remoteip
      * @param String $response
      *
      * @throws ValidatorException When missing remote ip
      *
      * @return Boolean
      */
    private function checkAnswer($secretKey, $remoteip, $response)
    {
        if ($remoteip == null || $remoteip == "") {
            throw new ValidatorException("vihuvac_recaptcha.validator.remote_ip");
        }

        // discard spam submissions
        if ($response == null || strlen($response) == 0) {
            return false;
        }


        $response = $this->httpGet(self::RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/siteverify", array(
            "secret"   => $secretKey,
            "remoteip" => $remoteip,
            "response" => $response
        ));

        return json_decode($response, true);
    }

    /**
     * Submits an HTTP POST to a reCAPTCHA server.
     *
     * @param String $host
     * @param String $path
     * @param Array  $data
     *
     * @return Array response
     */
    private function httpGet($host, $path, $data)
    {
        $host = sprintf("%s%s?%s", $host, $path, http_build_query($data, null, "&"));

        $context = $this->getResourceContext();

        return file_get_contents($host, false, $context);
    }

    /**
     * Resource context.
     *
     * @return resource context for HTTP Proxy.
     */
    private function getResourceContext()
    {
        if (null === $this->httpProxy["host"] || null === $this->httpProxy["port"]) {
            return null;
        }

        $options = array();
        foreach (array("http", "https") as $protocol) {
            $options[$protocol] = array(
                "method"          => "GET",
                "proxy"           => sprintf("tcp://%s:%s", $this->httpProxy["host"], $this->httpProxy["port"]),
                "request_fulluri" => true
            );

            if (null !== $this->httpProxy["auth"]) {
                $options[$protocol]["header"] = sprintf("Proxy-Authorization: Basic %s", base64_encode($this->httpProxy["auth"]));
            }
        }

        return stream_context_create($options);
    }
}
