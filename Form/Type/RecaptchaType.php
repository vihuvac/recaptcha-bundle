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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A field for entering a recaptcha text.
 */
class RecaptchaType extends AbstractType
{
    /**
     * The reCAPTCHA server URL's
     */
    const RECAPTCHA_API_SERVER        = "http://www.google.com/recaptcha/api";
    const RECAPTCHA_API_SECURE_SERVER = "https://www.google.com/recaptcha/api";

    /**
     * The public key
     *
     * @var string
     */
    protected $siteKey;

    /**
     * Use secure url?
     *
     * @var Boolean
     */
    protected $secure;

    /**
     * Enable recaptcha?
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * Language
     *
     * @var string
     */
    protected $language;


    /**
     * Construct.
     *
     * @param string    $siteKey    Recaptcha site key
     * @param string    $secure     Recaptcha securey api url
     * @param Boolean   $enabled    Recaptcha status
     * @param string    $language   Language or locale code
     */
    public function __construct(ContainerInterface $container)
    {
        $this->siteKey  = $container->getParameter("vihuvac_recaptcha.site_key");
        $this->secure   = $container->getParameter("vihuvac_recaptcha.secure");
        $this->enabled  = $container->getParameter("vihuvac_recaptcha.enabled");
        $this->language = $container->getParameter($container->getParameter("vihuvac_recaptcha.locale_key"));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace(
            $view->vars,
            array(
                "vihuvac_recaptcha_enabled" => $this->enabled
            )
        );

        if (!$this->enabled) {
            return;
        }

        if ($this->secure) {
            $server = self::RECAPTCHA_API_SECURE_SERVER;
        } else {
            $server = self::RECAPTCHA_API_SERVER;
        }

        $view->vars = array_replace(
            $view->vars,
            array(
                "url_challenge" => sprintf("%s.js?hl=%s", $server, $this->language),
                "site_key"      => $this->siteKey
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                "compound"      => false,
                "site_key"      => null,
                "url_challenge" => null,
                "attr"          => array(
                    "options" => array(
                        "theme" => null,
                        "type"  => null
                    )
                )
            )
        );
    }

    /**
     * Gets the Javascript source URLs.
     *
     * @param string $key The script name
     *
     * @return string The javascript source URL
     */
    public function getScriptURL($key)
    {
        return isset($this->scripts[$key]) ? $this->scripts[$key] : null;
    }

    /**
     * Gets the public key.
     *
     * @return string The javascript source URL
     */
    public function getSiteKey()
    {
        return $this->siteKey;
    }
}
