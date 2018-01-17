<?php

/**
 * This file is part of the Recaptcha package.
 *
 * (c) Víctor Hugo Valle Castillo <victouk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vihuvac\Bundle\RecaptchaBundle\Tests\Locale;

use Vihuvac\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use PHPUnit\Framework\TestCase;

class LocaleResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolveWithLocaleFromRequest()
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method("getLocale");

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->once())
            ->method("getCurrentRequest")
            ->willReturn($request)
        ;

        $resolver = new LocaleResolver("foo", true, $requestStack);
        $resolver->resolve();
    }

    /**
     * @test
     */
    public function resolveWithDefaultLocale()
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack
            ->expects($this->never())
            ->method("getCurrentRequest")
        ;

        $resolver = new LocaleResolver("foo", false, $requestStack);
        $resolver->resolve();
    }
}
