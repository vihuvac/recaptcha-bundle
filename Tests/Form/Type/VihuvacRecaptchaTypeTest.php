<?php

/**
 * This file is part of the Recaptcha package.
 *
 * (c) Víctor Hugo Valle Castillo <victouk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vihuvac\Bundle\RecaptchaBundle\Tests\Form\Type;

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType;
use Vihuvac\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VihuvacRecaptchaTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VihuvacRecaptchaType
     */
    protected $type;


    protected function setUp()
    {
        $requestStack   = $this->createMock(RequestStack::class);
        $localeResolver = new LocaleResolver("fr", false, $requestStack);
        $this->type     = new VihuvacRecaptchaType("key", true, true, $localeResolver);
    }

    /**
     * @test
     */
    public function buildView()
    {
        $view = new FormView();

        /**
         * @var FormInterface $form
         */
        $form = $this->createMock(FormInterface::class);

        $this->assertArrayNotHasKey("vihuvac_recaptcha_enabled", $view->vars);
        $this->assertArrayNotHasKey("vihuvac_recaptcha_ajax", $view->vars);

        $this->type->buildView($view, $form, array());

        $this->assertTrue($view->vars["vihuvac_recaptcha_enabled"]);
        $this->assertTrue($view->vars["vihuvac_recaptcha_ajax"]);
    }

    /**
     * @test
     */
    public function getParent()
    {
        $this->assertSame(TextType::class ?: "text", $this->type->getParent());
    }

    /**
     * @test
     */
    public function getSiteKey()
    {
        $this->assertSame("key", $this->type->getSiteKey());
    }

    /**
     * @test
     */
    public function configureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->type->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve();

        $expected = array(
            "compound"      => false,
            "language"      => "fr",
            "public_key"    => null,
            "url_challenge" => null,
            "url_noscript"  => null,
            "attr"          => array(
                "options" => array(
                    "theme"           => "dark",
                    "type"            => "audio",
                    "size"            => "normal",
                    "callback"        => null,
                    "expiredCallback" => null,
                    "defer"           => false,
                    "async"           => false
                )
            )
        );

        $this->assertSame($expected, $options);
    }

    /**
     * @test
     */
    public function getBlockPrefix()
    {
        $this->assertEquals("vihuvac_recaptcha", $this->type->getBlockPrefix());
    }
}
