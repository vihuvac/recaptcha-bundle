VihuvacRecaptchaBundle
======================

This bundle provides easy reCAPTCHA form field for Symfony in order to protect your website from spam and abuse.

[![Latest Stable Version](https://poser.pugx.org/vihuvac/recaptcha-bundle/v/stable)](https://packagist.org/packages/vihuvac/recaptcha-bundle) [![Latest Unstable Version](https://poser.pugx.org/vihuvac/recaptcha-bundle/v/unstable)](https://packagist.org/packages/vihuvac/recaptcha-bundle) [![Gitter](https://badges.gitter.im/vihuvac/recaptcha-bundle.svg)](https://gitter.im/vihuvac/recaptcha-bundle?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge) [![License](https://poser.pugx.org/vihuvac/recaptcha-bundle/license)](https://packagist.org/packages/vihuvac/recaptcha-bundle)

[![Total Downloads](https://poser.pugx.org/vihuvac/recaptcha-bundle/downloads)](https://packagist.org/packages/vihuvac/recaptcha-bundle) [![Monthly Downloads](https://poser.pugx.org/vihuvac/recaptcha-bundle/d/monthly)](https://packagist.org/packages/vihuvac/recaptcha-bundle) [![Daily Downloads](https://poser.pugx.org/vihuvac/recaptcha-bundle/d/daily)](https://packagist.org/packages/vihuvac/recaptcha-bundle)

Branch | Travis | Coveralls |
------ | ------ | --------- |
master | [![Build Status](https://travis-ci.org/vihuvac/recaptcha-bundle.svg?branch=master)](https://travis-ci.org/vihuvac/recaptcha-bundle) | [![Coverage Status](https://coveralls.io/repos/github/vihuvac/recaptcha-bundle/badge.svg?branch=master)](https://coveralls.io/github/vihuvac/recaptcha-bundle?branch=master) |

## Installation

### Step 1: Using composer and enable the Bundle

To install the bundle via composer, just run from the command line (terminal):

```bash
$ composer require vihuvac/recaptcha-bundle
```

Composer will automatically download all the required files, and install them for you. All that is left to do is to update your ```AppKernel.php``` file, and register the new bundle:

```php
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

```yaml
# app/config/config.yml

vihuvac_recaptcha:
    site_key:   here_is_your_site_key
    secret_key: here_is_your_secret_key
    locale_key: kernel.default_locale
```

> **NOTE**:
>
> This bundle uses a secure API (**HTTPS Protocol**). **Google API** solves the requests by the Browser (**Client**).
>
> The ```site_key``` parameter is the same than the ```public_key``` parameter and the ```secret_key``` parameter is the same than the ```private_key``` parameter (parameters used in the previous versions).

You can easily **enable** and **disable** the reCAPTCHA feature using any one of the booleans ```true``` or ```false``` through the **enabled** parameter, e.g:

```yaml
# app/config/config.yml

vihuvac_recaptcha:
    // ...
    enabled: true
```

If you want to use the language used by the locale request as the language for the reCAPTCHA, you must activate the resolver (deactivated by default):

```yaml
# app/config/config.yml

vihuvac_recaptcha:
    // ...
    locale_from_request: true
```

You can load the reCAPTCHA using the Ajax API (**optional**):

```yaml
# app/config/config.yml

vihuvac_recaptcha:
    // ...
    ajax: true
```

Additionally you can add HTTP Proxy configuration (**optional**):

```yaml
# app/config/config.yml

vihuvac_recaptcha:
    // ...
    host: proxy.your-domain.com
    port: 3128
    auth: proxy_username:proxy_password
```
In case you have turned off the domain name checking on reCAPTCHA's end, you'll need to check the origin of the response by enabling the ```verify_host``` option:

```yaml
# app/config/config.yml

vihuvac_recaptcha:
    // ...
    verify_host: true
```

Congratulations! You're ready!

## Basic Usage

When creating a new form class add the following line to create the field:

##### Symfony and PHP Reference

Package  | Symfony              | PHP                |
-------- | -------------------- | ------------------ |
Version  | **~2.3** to **~2.7** | **5.3** to **5.6** |

```php
<?php

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", "vihuvac_recaptcha");
    // ...
}
```

##### Symfony and PHP Reference

Package  | Symfony              | PHP                |
-------- | -------------------- | ------------------ |
Version  | **~2.8**             | **5.5** to **7.1** |
Version  | **~3.0** to **~3.3** | **5.5** to **7.1** |

> **Note**:
>
> To denote the form type, you have to use the fully qualified class name - like ```TextType::class``` in PHP 5.5+ or ```Symfony\Component\Form\Extension\Core\Type\TextType```.
> Before Symfony 2.8, you could use an alias for each type like ```text``` or ```date```.
> The old alias syntax will still work until Symfony 3.0. For more details, see the [2.8 UPGRADE Log](https://github.com/symfony/symfony/blob/2.8/UPGRADE-2.8.md#form "2.8 UPGRADE Log - Official Doc").

```php
<?php

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType as RecaptchaType;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", RecaptchaType::class);
    // ...
}
```

You can pass extra options to reCAPTCHA with the ```attr > options``` option, e.g:

```php
<?php

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType as RecaptchaType;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", RecaptchaType::class, array(
        "attr" => array(
            "options" => array(
                "theme" => "light",
                "type"  => "audio",
                "size"  => "normal",
                "defer" => false,   // Set true if you want to use the Ajax API.
                "async" => false    // Set true if you want to use the Ajax API.
            )
        )
    ));
    // ...
}
```

reCAPTCHA tag attributes and render parameters:

| Tag attribute         | Render parameter |     Value        | Default |                Description               |
| --------------------- | :--------------: | :--------------: | :-----: | ---------------------------------------: |
| data-theme            | theme            | dark / light     | light   | Optional. The color theme of the widget. |
| data-type             | type             | audio / image    | image   | Optional. The type of CAPTCHA to serve.  |
| data-size             | size             | compact / normal | normal  | Optional. The size of the widget.        |
| data-expired-callback | expiredCallback  |                  |         | Optional. The name of your callback function to be executed when the recaptcha response expires and the user needs to solve a new CAPTCHA. |
|                       | defer            | true / false     | false   | Optional for the Ajax API.               |
|                       | async            | true / false     | false   | Optional for the Ajax API.               |

Support Google's Invisible reCAPTCHA! It's super easy:

```php
<?php

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType as RecaptchaType;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", RecaptchaType::class, array(
        "attr" => array(
            "options" => array(
                "theme"    => "light",
                "type"     => "image",
                "size"     => "invisible",          // Set size to the invisible reCAPTCHA.
                "defer"    => false,                // Set true if you are using the Ajax API.
                "async"    => false,                // Set true if you are using the Ajax API.
                "callback" => "onReCaptchaSuccess", // Callback will be set by default if it's not defined (along with JS function that validates the form on success).
                "bind"     => "buttonSubmit",       // This is the form submit button id (html attribute).
                // ...
             )
        )
    ));
    // ...
}
```

> **Note**:
> If you use the pre-defined callback, you would need to add ```recaptcha-form``` class to your ```<form>``` tag.

If you need to configure the language for the reCAPTCHA depending on your site language (ideal for multi-language sites) you can pass the language with the "language" option:

```php
<?php

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType as RecaptchaType;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", RecaptchaType::class, array(
        "language" => "en",
        // ...
    ));
    // ...
}
```


To validate the field use:

```php
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

```php
<?php

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType as RecaptchaType;
use Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", RecaptchaType::class, array(
        "attr" => array(
            "options" => array(
                "theme" => "light",
                "type"  => "audio",
                "size"  => "normal"
            )
        ),
        "mapped"      => false,
        "constraints" => array(
            new RecaptchaTrue()
        )
    ));
    // ...
```


Cool! The form template resource is now auto registered via container extension.
However, you can always implement your own custom form widget:

**PHP**:

```php
<?php $view["form"]->setTheme($form, array("VihuvacRecaptchaBundle:Form")) ?>

<?php echo $view["form"]->widget($form["recaptcha"], array(
    "attr" => array(
        "options" => array(
            "theme" => "light",
            "type"  => "audio",
            "size"  => "normal"
        )
    )
)) ?>
```

**Twig**:

```twig
{% form_theme form "VihuvacRecaptchaBundle:Form:vihuvac_recaptcha_widget.html.twig" %}

{{
    form_widget(
        form.recaptcha, {
            "attr": {
                "options": {
                    "theme": "light",
                    "type": "audio",
                    "size": "normal"
                },
            }
        }
    )
}}
```

If you are not using a form, you can still implement the reCAPTCHA field using JavaScript:

**PHP**:

```php
<div id="recaptcha-container"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $.getScript("<?php echo \Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType::RECAPTCHA_API_JS_SERVER ?>", function() {
            Recaptcha.create("<?php echo $form['recaptcha']->get('site_key') ?>", "recaptcha-container", {
                theme: "light",
                type: "audio",
                "size": "normal"
            });
        });
    };
</script>
```

**Twig**:

```twig
<div id="recaptcha-container"></div>
<script type="text/javascript">
    $(document).ready(function() {
        $.getScript("{{ constant('\\Vihuvac\\Bundle\\RecaptchaBundle\\Form\\Type\\VihuvacRecaptchaType::RECAPTCHA_API_JS_SERVER') }}", function() {
            Recaptcha.create("{{ form.recaptcha.get('site_key') }}", "recaptcha-container", {
                theme: "light",
                type: "audio",
                "size": "normal"
            });
        });
    });
</script>
```

**Customization**:

If you want to use a custom theme, put your chunk of code before setting the theme:

```twig
<div id="recaptcha_widget">
    <div id="recaptcha_image"></div>
    <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>

    <span class="recaptcha_only_if_image">Enter the words above:</span>
    <span class="recaptcha_only_if_audio">Enter the numbers you hear:</span>

    <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />

    <div><a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a></div>
    <div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type("audio")">Get an audio CAPTCHA</a></div>
    <div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type("image")">Get an image CAPTCHA</a></div>

    <div><a href="javascript:Recaptcha.showhelp()">Help</a></div>
 </div>

{% form_theme form "VihuvacRecaptchaBundle:Form:vihuvac_recaptcha_widget.html.twig %}

{{
    form_widget(
        form.recaptcha, {
            "attr": {
                "options" : {
                    "theme" : "custom",
                },
            }
        }
    )
}}
```

**Further reading**: [Google Official Doc](https://developers.google.com/recaptcha/ "Getting Started - Google Official Doc").

## Tests

Execute this command to run tests:

```bash
$ cd recaptcha-bundle/
$ ./vendor/bin/phpunit
```

> **Note**:
> If you are running tests only and within the bundle, as first step you should run ```composer install``` in order to install the required dependencies. Then you'll be able to run the tests!
