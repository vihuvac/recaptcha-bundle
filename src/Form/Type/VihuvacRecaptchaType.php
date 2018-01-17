<?php

/**
 * This file is part of the Recaptcha package.
 *
 * (c) Víctor Hugo Valle Castillo <victouk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vihuvac\Bundle\RecaptchaBundle\Form\Type;

use Vihuvac\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A field for entering a recaptcha text.
 */
class VihuvacRecaptchaType extends AbstractType
{
    /**
     * The reCAPTCHA Server URL's
     */
    const RECAPTCHA_API_SERVER    = "https://www.google.com/recaptcha/api";
    const RECAPTCHA_API_JS_SERVER = "https://www.google.com/recaptcha/api/js/recaptcha_ajax.js";

    /**
     * The public key
     *
     * @var String
     */
    protected $siteKey;

    /**
     * Enable recaptcha?
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * Use AJAX API
     *
     * @var Boolean
     */
    protected $ajax;

    /**
     * Language
     *
     * @var String
     */
    protected $localeResolver;


    /**
     * Construct.
     *
     * @param String    $siteKey            Recaptcha site key
     * @param Boolean   $enabled            Recaptcha status
     * @param Boolean   $ajax               Ajax status
     * @param String    $$localeResolver    Language or locale code
     */
    public function __construct($siteKey, $enabled, $ajax, LocaleResolver $localeResolver)
    {
        $this->siteKey        = $siteKey;
        $this->enabled        = $enabled;
        $this->ajax           = $ajax;
        $this->localeResolver = $localeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            "vihuvac_recaptcha_enabled" => $this->enabled,
            "vihuvac_recaptcha_ajax"    => $this->ajax
        ));

        if (!$this->enabled) {
            return;
        }

        if (!isset($options["language"])) {
            $options["language"] = $this->localeResolver->resolve();
        }

        if (!$this->ajax) {
            $view->vars = array_replace($view->vars, array(
                "url_challenge" => sprintf("%s.js?hl=%s", self::RECAPTCHA_API_SERVER, $options["language"]),
                "site_key"      => $this->siteKey
            ));
        } else {
            $view->vars = array_replace($view->vars, array(
                "url_api"  => self::RECAPTCHA_API_JS_SERVER,
                "site_key" => $this->siteKey
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                "compound"      => false,
                "language"      => $this->localeResolver->resolve(),
                "site_key"      => null,
                "url_challenge" => null,
                "url_noscript"  => null,
                "attr"          => array(
                    "options" => array(
                        "theme"           => "light",
                        "type"            => "image",
                        "size"            => "normal",
                        "callback"        => null,
                        "expiredCallback" => null,
                        "badge"           => null,
                        "bind"            => null,
                        "defer"           => false,
                        "async"           => false
                    )
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return "vihuvac_recaptcha";
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * Gets the Javascript source URLs.
     *
     * @param String $key The script name
     *
     * @return String The javascript source URL
     */
    public function getScriptURL($key)
    {
        return isset($this->scripts[$key]) ? $this->scripts[$key] : null;
    }

    /**
     * Gets the public key.
     *
     * @return String The javascript source URL
     */
    public function getSiteKey()
    {
        return $this->siteKey;
    }
}
