<?php

/*
 * This file is part of the Redlink PHP API Client Symfony Bundle.
 *
 * (c) Mateusz Żyła <mateusz.zylaa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new \RuntimeException('Install dependencies to run test suite.');
}
$autoload = require_once $file;