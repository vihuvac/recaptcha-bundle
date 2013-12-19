<?php

namespace Magnopus\Bundle\RecaptchaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/** @Annotation */
class True extends Constraint
{
    public $message = 'validate_captcha_value';

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
        return 'magnopus_recaptcha.true';
    }
}