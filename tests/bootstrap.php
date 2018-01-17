<?php

/**
 * This file is part of the Recaptcha package.
 *
 * (c) Víctor Hugo Valle Castillo <victouk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

error_reporting(-1);
date_default_timezone_set("UTC");

// Ensure that composer has installed all dependencies.
if (!file_exists(dirname(__DIR__) . "/composer.lock")) {
    die(
        "Dependencies must be installed using composer:

        curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
        composer install

        Visit http://getcomposer.org/ for more information."
    );
}

// Include the composer autoloader.
$loader = require dirname(__DIR__) . "/vendor/autoload.php";
