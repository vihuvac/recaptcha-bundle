VihuvacRecaptchaBundle
======================

[![Join the chat at https://gitter.im/vihuvac/recaptcha-bundle](https://badges.gitter.im/vihuvac/recaptcha-bundle.svg)](https://gitter.im/vihuvac/recaptcha-bundle?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

This bundle provides easy reCAPTCHA form field for Symfony in order to protect your website from spam and abuse.

[![License](http://tools.vihuvac.com/images/collection/git-docs/license-mit.svg)](https://github.com/vihuvac/recaptcha-bundle/blob/master/LICENSE)

## Installation

### Step 1: Using composer and enable the Bundle

To install VihuvacRecaptchaBundle with Composer just run via command line (terminal):

```bash
php composer.phar require vihuvac/recaptcha-bundle
```

Now, Composer will automatically download all required files, and install them for you. All that is left to do is to update your ```AppKernel.php``` file, and register the new bundle:

``` php
// app/AppKernel.php

<?php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Vihuvac\Bundle\RecaptchaBundle\VihuvacRecaptchaBundle(),
    );
}
```

### Step 2: Configure the bundle

Add the following to your config file:

``` yaml
# app/config/config.yml

vihuvac_recaptcha:
    site_key:   here_is_your_site_key
    secret_key: here_is_your_secret_key
    secure:     true
    enabled:    true
    locale_key: kernel.default_locale
```

> **NOTE**:
>
> If you want to use the secure URL for the reCAPTCHA, just set ```true``` as value in the secure parameter (__false is the default value__).
>
> The ```site_key``` parameter is the same than the ```public_key``` parameter and the ```secret_key``` parameter is the same than the ```private_key``` parameter (parameters used in the previous versions).

You can easily disable reCAPTCHA (for example in a local or test environment):

``` yaml
# app/config/config.yml

vihuvac_recaptcha:
    // ...
    enabled: false
```

Congratulations! You're ready!

## Basic Usage

When creating a new form class add the following line to create the field:

``` php
<?php

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", VihuvacRecaptchaType::class);
    // ...
}
```

You can pass extra options to reCAPTCHA with the ```attr > options``` option, e.g:

| Tag attribute | Render parameter |     Value     | Default |                Description               |
| ------------- | :--------------: | :-----------: | :-----: | ---------------------------------------: |
| data-theme    | theme            | dark / light  | light   | Optional. The color theme of the widget. |
| data-type     | type             | audio / image | image   | Optional. The type of CAPTCHA to serve.  |

``` php
<?php

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", VihuvacRecaptchaType::class,
        array(
            "attr" => array(
                "options" => array(
                    "theme" => "light",
                    "type"  => "audio"
                )
            )
        )
    );
    // ...
}
```

To validate the field use:

``` php
<?php

use Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * @Recaptcha\IsTrue
 */
public $recaptcha;
```

Another method would consist to pass the validation constraints as an options of your FormType. This way, your data class contains only meaningful properties.
If we take the example from above, the buildForm method would look like this.
Please note that if you set ```mapped => false``` then the annotation will not work. You have to also set ```constraints```:

``` php
<?php

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType;
use Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", VihuvacRecaptchaType::class,
        array(
            "attr" => array(
                "options" => array(
                    "theme" => "light",
                    "type"  => "audio"
                )
            ),
            "mapped"      => false,
            "constraints" => array(
                new RecaptchaTrue()
            )
        )
    );
    // ...
```


Cool, now you are ready to implement the form widget:

**PHP**:

``` php
<?php $view["form"]->setTheme($form, array("VihuvacRecaptchaBundle:Form")) ?>

<?php echo $view["form"]->widget($form["recaptcha"],
    array(
        "attr" => array(
            "options" => array(
                "theme" => "light",
                "type"  => "audio"
            )
        )
    ))
?>
```

**Twig**:

``` jinja
{% form_theme form "VihuvacRecaptchaBundle:Form:vihuvac_recaptcha_widget.html.twig" %}

{{
    form_widget(
        form.recaptcha, {
            "attr": {
                "options": {
                    "theme": "light",
                    "type": "audio"
                },
            }
        }
    )
}}
```

If you are not using a form, you can still implement the reCAPTCHA field
using JavaScript:

**PHP**:

``` php
<div id="recaptcha-container"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $.getScript("<?php echo \Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType::RECAPTCHA_API_JS_SERVER ?>", function() {
            Recaptcha.create("<?php echo $form['recaptcha']->get('site_key') ?>", "recaptcha-container", {
                theme: "light",
                type: "audio"
            });
        });
    };
</script>
```

**Twig**:

``` jinja
<div id="recaptcha-container"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $.getScript("{{ constant('\\Vihuvac\\Bundle\\RecaptchaBundle\\Form\\Type\\VihuvacRecaptchaType::RECAPTCHA_API_JS_SERVER') }}", function() {
            Recaptcha.create("{{ form.recaptcha.get('site_key') }}", "recaptcha-container", {
                theme: "light",
                type: "audio"
            });
        });
    });
</script>
```

**Further reading**: [Google Official Documentation](https://developers.google.com/recaptcha/docs/start)
