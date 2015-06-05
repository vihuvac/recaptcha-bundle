VihuvacRecaptchaBundle
======================

This bundle provides easy reCAPTCHA form field for Symfony in order to protect your website from spam and abuse.

[![License](http://tools.vihuvac.com/images/collection/git-docs/license-mit.svg)](https://packagist.org/packages/nietonfir/google-recaptcha-bundle)

## Installation

### Step 1: Using Composer (recommended)

To install VihuvacRecaptchaBundle with Composer just add the following to your ```composer.json``` file:

```js
// composer.json

{
    // ...
    "require": {
        // ...
        "vihuvac/recaptcha-bundle": "dev-master"
    }
}
```

> **NOTE**:
>
> Please replace ```dev-master``` in the snippet above with the latest stable branch, for example ```~2.0```.

Then, you can install the new dependencies by running Composer's ```update``` command from the directory where your ```composer.json``` file is located:

```bash
$ php composer.phar update
```

Now, Composer will automatically download all required files, and install them for you. All that is left to do is to update your ```AppKernel.php``` file, and register the new bundle:

```php
// AppKernel::registerBundles()

<?php

$bundles = array(
    // ...
    new Vihuvac\Bundle\RecaptchaBundle\VihuvacRecaptchaBundle(),
    // ...
);
```

### Step 1 (alternative): Using ```deps``` file (Symfony 2.0.*)

First, checkout a copy of the code. Just add the following to the ```deps``` file of your Symfony Standard Distribution:

```ini
[VihuvacRecaptchaBundle]
    git=http://github.com/vihuvac/recaptcha-bundle.git
    target=/bundles/Vihuvac/RecaptchaBundle
```

> **NOTE**:
>
> You can add ```version``` tag in the snippet above with the latest stable branch, for example ``version=origin/v2.0.0``.

Then register the bundle with your kernel:

```php
// AppKernel::registerBundles()

<?php

$bundles = array(
    // ...
    new Vihuvac\Bundle\RecaptchaBundle\VihuvacRecaptchaBundle(),
    // ...
);
```

Make sure that you also register the namespace with the autoloader:

```php
// app/autoload.php

<?php

$loader->registerNamespaces(array(
    // ...
    'Vihuvac'  =>  __DIR__.'/../vendor/bundles',
    // ...
));
```

Now use the ```vendors``` script to clone the newly added repositories into your project:

```bash
$ php bin/vendors install
```

### Step 1 (alternative): Using submodules (Symfony 2.0.*)

If you're managing your vendor libraries with submodules, first create the ```vendor/bundles/vihuvac``` directory:

``` bash
$ mkdir -pv vendor/bundles/vihuvac
```

Next, add the necessary submodule:

``` bash
$ git submodule add git://github.com/vihuvac/recaptcha-bundle.git vendor/bundles/vihuvac/recaptcha-bundle
```

### Step2: Configure the autoloader

Add the following entry to your autoloader:

``` php
// app/autoload.php

<?php

$loader->registerNamespaces(array(
    // ...
    'Vihuvac' => __DIR__.'/../vendor/bundles',
));
```

### Step3: Enable the bundle

Finally, enable the bundle in the kernel:

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

### Step4: Configure the bundle's

Finally, add the following to your config file:

``` yaml
# app/config/config.yml

vihuvac_recaptcha:
    site_key:   here_is_your_site_key
    secret_key: here_is_your_secret_key
    secure:     false
    locale_key: kernel.default_locale
```

> **NOTE**:
>
> If you want to use the secure URL for the reCAPTCHA, just set ```true``` as value in the secure parameter (__false is the default value__).
>
> The ```site_key is``` the same than the ```public_key``` and the ```secret_key``` is the same than the ```private_key```.

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

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", "vihuvac_recaptcha");
    // ...
}
```

You can pass extra options to reCAPTCHA with the ```attr > options``` option, e.g the theme, by default it is light, but you can update it as follows:

``` php
<?php

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", "vihuvac_recaptcha",
        array(
            "attr" => array(
                "options" => array(
                    "theme" => "dark"
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
 * @Recaptcha\True
 */
public $recaptcha;
```

Another method would consist to pass the validation constraints as an options of your FormType. This way, your data class contains only meaningful properties.
If we take the example from above, the buildForm method would look like this.
Please note that if you set ```mapped => false``` then the annotation will not work. You have to also set ```constraints```:

``` php
<?php

use Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints\True;

public function buildForm(FormBuilder $builder, array $options)
{
    // ...
    $builder->add("recaptcha", "vihuvac_recaptcha",
        array(
            "attr" => array(
                "options" => array(
                    "theme" => "dark"
                )
            ),
            "mapped"      => false,
            "constraints" => array(
                new True()
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
                "theme" => "dark"
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
                "options" : {
                    "theme" : "dark",
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
        $.getScript("<?php echo \Vihuvac\Bundle\RecaptchaBundle\Form\Type\RecaptchaType::RECAPTCHA_API_JS_SERVER ?>", function() {
            Recaptcha.create("<?php echo $form['recaptcha']->get('site_key') ?>", "recaptcha-container", {
                theme: "dark",
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
        $.getScript("{{ constant('\\Vihuvac\\Bundle\\RecaptchaBundle\\Form\\Type\\RecaptchaType::RECAPTCHA_API_JS_SERVER') }}", function() {
            Recaptcha.create("{{ form.recaptcha.get('site_key') }}", "recaptcha-container", {
                theme: "dark"
            });
        });
    });
</script>
```

**Further reading**: [Google Official Documentation](https://developers.google.com/recaptcha/docs/start)
