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

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class IsTrue extends Constraint
{
    /**
     * The reCAPTCHA validation message
     */
    public $message = "vihuvac_recaptcha.validator.message";

    /**
     * The reCAPTCHA validation message
     */
    public $invalidHostMessage = "vihuvac_recaptcha.validator.invalidHostMessage";


    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return "vihuvac_recaptcha.true";
    }
}
