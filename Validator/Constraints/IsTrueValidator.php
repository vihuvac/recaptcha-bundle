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
     * Construct.
     *
     * @param Boolean      $enabled
     * @param string       $secretKey
     * @param RequestStack $requestStack
     * @param array        $httpProxy
     */
    public function __construct($enabled, $secretKey, RequestStack $requestStack, array $httpProxy)
    {
	    $this->enabled      = $enabled;
	    $this->secretKey    = $secretKey;
	    $this->requestStack = $requestStack;
	    $this->httpProxy    = $httpProxy;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // if recaptcha is disabled, always valid
        if (!$this->enabled) {
            return true;
        }

        // define variables for recaptcha check answer
	    $remoteip = $this->requestStack->getMasterRequest()->getClientIp();
	    $response = $this->requestStack->getMasterRequest()->get("g-recaptcha-response");

	    $isValid = $this->checkAnswer($this->secretKey, $remoteip, $response);

        if (!$isValid) {
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

	    $response = json_decode($response, true);

	    if ($response["success"] == true) {
		    return true;
	    }

        return false;
    }

	/**
	 * Submits an HTTP POST to a reCAPTCHA server.
	 *
	 * @param string $host
	 * @param string $path
	 * @param array  $data
	 *
	 * @return array response
	 */
	private function httpGet($host, $path, $data)
	{
		$host = sprintf("%s%s?%s", $host, $path, http_build_query($data, null, '&'));

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
				"request_fulluri" => true,
			);

			if (null !== $this->httpProxy["auth"]) {
				$options[$protocol]["header"] = sprintf("Proxy-Authorization: Basic %s", base64_encode($this->httpProxy["auth"]));
			}
		}

		return stream_context_create($options);
	}
}
