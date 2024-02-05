<?php

namespace Cardlink\Payments\Helpers;

/**
 * Collection of helper methods for use in cryptographic operations.
 * 
 * @package Cardlink\Payments\Helpers
 */
class Crypto
{
    public static function calculateDigest($input)
    {
        return base64_encode(hash('sha256', ($input), true));
    }

    public static function calculateSignature(array $data, string $privateKey): string
    {
        $string = '';
        foreach ($data as $value) {
            if (!isset($value) || $value == '') {
                continue;
            }
            $string .= $value . ';';
        }
        $data_to_sign = \Cardlink\Payments\Helpers\Tools::convertToUtf8($string);

        if (\Cardlink\Payments\Helpers\Tools::startsWith($privateKey, '-----BEGIN CERTIFICATE-----')) {
            $pkey = openssl_pkey_get_private($privateKey);
            $pkey_data = openssl_pkey_get_details($pkey);
            $private_key = $pkey_data['key'];
        } else if (\Cardlink\Payments\Helpers\Tools::startsWith($privateKey, '-----BEGIN PRIVATE KEY-----')) {
            $private_key = $privateKey;
        } else {
            throw new \Exception('Invalid certificate used for method ' . __METHOD__);
        }

        openssl_sign($data_to_sign, $signature, $private_key, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    public static function validateSignature(array $data, string $signature, string $certificate): bool
    {
        $string = '';
        foreach ($data as $value) {
            if (!isset($value) || $value == '') {
                continue;
            }
            $string .= $value . ';';
        }
        $data_to_sign = \Cardlink\Payments\Helpers\Tools::convertToUtf8($string);

        if (\Cardlink\Payments\Helpers\Tools::startsWith($certificate, '-----BEGIN CERTIFICATE-----')) {
            $pkey = openssl_pkey_get_public($certificate);
            $pkey_data = openssl_pkey_get_details($pkey);
            $public_key = $pkey_data['key'];
        } else if (\Cardlink\Payments\Helpers\Tools::startsWith($certificate, '-----BEGIN PUBLIC KEY-----')) {
            $public_key = $certificate;
        } else {
            throw new \Exception('Invalid certificate used for method ' . __METHOD__);
        }

        $ok = openssl_verify($data_to_sign, base64_decode($signature), $public_key, OPENSSL_ALGO_SHA256); // "sha256WithRSAEncryption");

        if ($ok == 1) {
            return true;
        } else {
            error_log("error: " . openssl_error_string());
            return false;
        }
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Generates a GUID v4 string.
     * 
     * @return string
     */
    public static function generateGuid(): string
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((double) microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $guidv4 =
            substr($charid, 0, 8) . $hyphen .
            substr($charid, 8, 4) . $hyphen .
            substr($charid, 12, 4) . $hyphen .
            substr($charid, 16, 4) . $hyphen .
            substr($charid, 20, 12);
        return $guidv4;
    }

    public static function generateRfCode($orderId, $amount, $diasCustomerCode)
    {
        /* calculate payment check code */
        $paymentSum = 0;
        if ($amount > 0) {
            $ordertotal = str_replace([','], '.', (string) $amount);
            $ordertotal = number_format($ordertotal, 2, '', '');
            $ordertotal = strrev($ordertotal);
            $factor = [1, 7, 3];
            $idx = 0;
            for ($i = 0; $i < strlen($ordertotal); $i++) {
                $idx = $idx <= 2 ? $idx : 0;
                $paymentSum += $ordertotal[$i] * $factor[$idx];
                $idx++;
            }
        }

        $randomNumber = str_pad($orderId, 13, '0', STR_PAD_LEFT);
        $paymentCode = $paymentSum ? ($paymentSum % 8) : '8';
        $systemCode = '12';
        $tempCode = $diasCustomerCode . $paymentCode . $systemCode . $randomNumber . '271500';
        $mod97 = bcmod($tempCode, '97');

        $cd = 98 - (int) $mod97;
        $cd = str_pad((string) $cd, 2, '0', STR_PAD_LEFT);
        $rf_payment_code = 'RF' . $cd . $diasCustomerCode . $paymentCode . $systemCode . $randomNumber;

        return $rf_payment_code;
    }
}