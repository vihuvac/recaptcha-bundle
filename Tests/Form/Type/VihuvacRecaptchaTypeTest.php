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

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType as RecaptchaType;
use Vihuvac\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Tests that tags are transformed correctly using the data transformer.
 *
 * See https://symfony.com/doc/current/testing/database.html
 */
class VihuvacRecaptchaTypeTest extends TypeTestCase
{
    /**
     * @var RecaptchaType $type
     */
    protected $type;


    protected function setUp()
    {
        $requestStack   = $this->createMock(RequestStack::class);
        $localeResolver = new LocaleResolver("en", false, $requestStack);
        $this->type     = new RecaptchaType("key", true, true, $localeResolver);
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

        $expected = array(
            "compound"      => false,
            "language"      => "en",
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
                    "bind"            => null,
                    "badge"           => null,
                    "defer"           => false,
                    "async"           => false
                )
            )
        );

        $this->assertSame($expected, $optionsResolver->resolve($expected));
    }

    /**
     * @test
     */
    public function getBlockPrefix()
    {
        $this->assertEquals("vihuvac_recaptcha", $this->type->getBlockPrefix());
    }
}
