<?php

/**
 * This file is part of the Recaptcha package.
 *
 * (c) Víctor Hugo Valle Castillo <victouk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vihuvac\Bundle\RecaptchaBundle\Locale;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Depending on the configuration resolves the correct locale for the reCAPTCHA.
 */
final class LocaleResolver
{
    /**
     * @var String
     */
    private $defaultLocale;

    /**
     * @var Boolean
     */
    private $useLocaleFromRequest;

    /**
     * @var RequestStack
     */
    private $requestStack;


    /**
     * @param String       $defaultLocale
     * @param Boolean      $useLocaleFromRequest
     * @param RequestStack $requestStack
     */
    public function __construct($defaultLocale, $useLocaleFromRequest, RequestStack $requestStack)
    {
        $this->defaultLocale        = $defaultLocale;
        $this->useLocaleFromRequest = $useLocaleFromRequest;
        $this->requestStack         = $requestStack;
    }

    /**
     * @return String The resolved locale key, depending on configuration
     */
    public function resolve()
    {
        return $this->useLocaleFromRequest
            ? $this->requestStack->getCurrentRequest()->getLocale()
            : $this->defaultLocale
        ;
    }
}
