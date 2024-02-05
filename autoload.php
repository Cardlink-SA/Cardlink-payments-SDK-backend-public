<?php

require_once 'vendor/autoload.php';

spl_autoload_register('cardlink_payments_sdk_autoloader');

/**
 * Copyright (c) 2023 Cardlink S.A.
 *
 * @author      Cardlink S.A.
 * @category    In-App Payments SDK
 * @package     Cardlink In-App Payments
 * @version     1.0.0
 * @copyright   Copyright (c) 2023 Cardlink S.A. (https://www.cardlink.gr)
 * @license     GNU General Public License v3.0
 */
function cardlink_payments_sdk_autoloader($class)
{
    // replace namespace separators with directory separators in the relative 
    // class name, append with .php
    $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $file = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class_path . '.php';
    // if the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
}