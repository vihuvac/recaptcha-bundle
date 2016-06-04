<!--{#

    This file is part of the Recaptcha package.

    (c) Víctor Hugo Valle Castillo <victouk@gmail.com>

    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.

#}-->

<?php if ($vihuvac_recaptcha_enabled): ?>
    <script src="<?php echo $url_challenge ?>" type="text/javascript"></script>
    <div
        class="g-recaptcha"
        data-theme="<?php echo $attr['options']['theme'] ?>"
        data-type="<?php echo $attr['options']['type'] ?>"
        data-sitekey="<?php echo $site_key ?>"
    ></div>
    <noscript>
        <div style="width: 302px; height: 352px;">
            <div style="width: 302px; height: 352px; position: relative;">
                <div style="width: 302px; height: 352px; position: absolute;">
                    <iframe
                        src="https://www.google.com/recaptcha/api/fallback?k=<?php echo $site_key ?>"
                        frameborder="0"
                        scrolling="no"
                        style="width: 302px; height:352px; border-style: none;"
                    ></iframe>
                </div>
                <div style="width: 250px; height: 80px; position: absolute; border-style: none; bottom: 21px; left: 25px; margin: 0px; padding: 0px; right: 25px;">
                    <textarea
                        id="g-recaptcha-response"
                        name="g-recaptcha-response"
                        class="g-recaptcha-response"
                        style="width: 250px; height: 80px; border: 1px solid #c1c1c1; margin: 0px; padding: 0px; resize: none;"
                    ></textarea>
                </div>
            </div>
        </div>
    </noscript>
<?php endif ?>
