<?php

namespace Cardlink\Payments\Helpers;

/**
 * Collection of helper methods for generic use.
 * 
 * @package Cardlink\Payments\Helpers
 */
class Tools
{

    /**
     * Convert the characters of the input string to UTF-8 encoding.
     * 
     * @param string $input The string to encode.
     * @return string The UTF-8 encoded string.
     */
    public static function convertToUtf8(string $input): string
    {
        $phpVersion = phpversion();

        if (
            version_compare($phpVersion, '8.2.0', '<')
            && function_exists('utf8_encode')
        ) {
            return utf8_encode($input);
        } else {
            return mb_convert_encoding($input, 'UTF-8');
        }
    }

    /**
     * Extract the value of a key from the array. If the key does not exist, return a default value.
     * 
     * @param array $data The source data array.
     * @param string $key The key to extract data for.
     * @param mixed|null $default The default/fallback value to return if the key does not exist in the source array.
     * 
     * @return mixed
     */
    public static function extractFromArray(array $data, string $key, $default = null)
    {
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }
        return $default;
    }

    /**
     * Find out if the $string parameter starts with the contents of $startString.
     * 
     * @param string $input The string to be tested.
     * @param string $startString The string to search for at the start of $string.
     * 
     * @return bool
     */
    public static function startsWith(string $input, string $startString): bool
    {
        $len = strlen($startString);

        return (substr($input, 0, $len) === $startString);
    }

    /**
     * Create the HTML code of an auto-submitting form.
     * 
     * @param string $action The URL to submit the form data to.
     * @param array $data The data to submit.
     * @param string $method The form submit method (GET/POST). Default is POST.
     * @param string $formName The name of the form. If not set, a random value is generated every time.
     * @return string
     */
    public static function createAutoSubmittingForm(string $action, array $data, string $method = 'post', string $formName = null): string
    {
        $formName ??= \Cardlink\Payments\Helpers\Crypto::generateRandomString(10);

        @ob_start();
        ?>
        <form name="<?php echo $formName; ?>" id="<?php echo $formName; ?>" method="<?php echo strtolower($method); ?>"
            action="<?php echo $action; ?>">
            <?php foreach ($data as $key => $value): ?>
                <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>" />
            <?php endforeach; ?>
        </form>
        <script>
            window.onload = function () {
                document.forms['<?php echo $formName; ?>'].submit();
            };
        </script>
        <?php

        $html = ob_get_contents();
        @ob_end_clean();

        return $html;
    }

    /**
     * Generate the Request Fund (RF) code for IRIS payments.
     * @param string $diasCustomerCode The DIAS customer code of the merchant.
     * @param mixed $orderId The ID of the order.
     * @param mixed $amount The amount due.
     * @return string The generated RF code.
     */
    public static function generateIrisRFCode(string $diasCustomerCode, $orderId, $amount)
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

        $orderIdNum = (int) filter_var($orderId, FILTER_SANITIZE_NUMBER_INT);

        $randomNumber = str_pad($orderIdNum, 13, '0', STR_PAD_LEFT);
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