<!--{#

    This file is part of the Recaptcha package.

    (c) Víctor Hugo Valle Castillo <victouk@gmail.com>

    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.

#}-->

<?php if ($vihuvac_recaptcha_enabled): ?>
    <?php if (isset($attr["options"])): ?>
        <script type="text/javascript">
            var onloadCallback = function() {
                grecaptcha.render(
                    "recaptcha", {
                        "sitekey" : <?php echo $view["form"]->widget($form["public_key"]) ?>,
                        "theme"   : <?php echo $view["form"]->block($form, "widget_attributes") ?>
                    }
                );
            };
        </script>
    <?php endif ?>
    <script type="text/javascript" src="<?php echo $view["form"]->widget($form["url_api_server"]) ?>" async defer></script>
    <div id="recaptcha"></div>
<?php endif ?>
