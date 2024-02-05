<?php

namespace Cardlink\Payments\Settings;

/**
 * Data model for storing settings for the platform endpoints that will handle payment gateway responses.
 * 
 * @package Cardlink\Payments\Settings
 */
class RouteSettings
{
    /**
     * The URL that the mobile clients will send customer and order information to initiate a card payment transaction.
     * @var string
     */
    private string $paymentRequestUrl = '';

    /**
     * The URL to redirect the customer to after a successful payment transaction.
     * @var string
     */
    private string $paymentSuccessUrl = '';

    /**
     * The URL to redirect the customer to after a failed payment transaction.
     * @var string
     */
    private string $paymentFailedUrl = '';

    /**
     * The URL that the mobile clients will send customer and order information to initiate an IRIS payment transaction.
     * @var string
     */
    private string $irisPaymentRequestUrl = '';

    /**
     * The URL to redirect the customer to after a successful IRIS payment transaction.
     * @var string
     */
    private string $irisPaymentSuccessUrl = '';

    /**
     * The URL to redirect the customer to after a failed IRIS payment transaction.
     * @var string
     */
    private string $irisPaymentFailedUrl = '';

    /**
     * The URL that the mobile clients will send customer and order information to initiate a PayPal payment transaction.
     * @var string
     */
    private string $paypalPaymentRequestUrl = '';

    /**
     * The URL to redirect the customer to after a successful PayPal payment transaction.
     * @var string
     */
    private string $paypalPaymentSuccessUrl = '';

    /**
     * The URL to redirect the customer to after a failed PayPal payment transaction.
     * @var string
     */
    private string $paypalPaymentFailedUrl = '';

    /**
     * The URL of the custom UI stylesheets code (CSS).
     * @var string
     */
    private string $uiStyleSheetsUrl = '';

    /**
     * Get the URL that the mobile clients will send customer and order information to initiate a card payment transaction.
     *
     * @return string
     */
    public function getPaymentRequestUrl(): string
    {
        return $this->paymentRequestUrl;
    }

    /**
     * Set the URL that the mobile clients will send customer and order information to initiate a card payment transaction.
     *
     * @param string $paymentRequestUrl The URL that the mobile clients will send customer and order information to initiate a card payment transaction.
     *
     * @return $this
     */
    public function setPaymentRequestUrl(string $paymentRequestUrl): \Cardlink\Payments\Settings\RouteSettings
    {
        $this->paymentRequestUrl = trim($paymentRequestUrl);

        return $this;
    }

    /**
     * Get the URL to redirect the customer to after a successful payment transaction.
     *
     * @return string
     */
    public function getPaymentSuccessUrl(): string
    {
        return $this->paymentSuccessUrl;
    }

    /**
     * Set the URL to redirect the customer to after a successful payment transaction.
     *
     * @param string $paymentSuccessUrl The URL to redirect the customer to after a successful payment transaction.
     *
     * @return $this
     */
    public function setPaymentSuccessUrl(string $paymentSuccessUrl): \Cardlink\Payments\Settings\RouteSettings
    {
        $this->paymentSuccessUrl = trim($paymentSuccessUrl);

        return $this;
    }

    /**
     * Get the URL to redirect the customer to after a failed payment transaction.
     *
     * @return string
     */
    public function getPaymentFailedUrl(): string
    {
        return $this->paymentFailedUrl;
    }

    /**
     * Set the URL to redirect the customer to after a failed payment transaction.
     *
     * @param string $paymentFailedUrl  The URL to redirect the customer to after a failed payment transaction.
     *
     * @return $this
     */
    public function setPaymentFailedUrl(string $paymentFailedUrl): \Cardlink\Payments\Settings\RouteSettings
    {
        $this->paymentFailedUrl = trim($paymentFailedUrl);

        return $this;
    }

    /**
     * Get the URL that the mobile clients will send customer and order information to initiate an IRIS payment transaction.
     *
     * @return string
     */
    public function getIrisPaymentRequestUrl(): string
    {
        return $this->irisPaymentRequestUrl;
    }

    /**
     * Set the URL that the mobile clients will send customer and order information to initiate an IRIS payment transaction.
     *
     * @param string $paymentRequestUrl The URL that the mobile clients will send customer and order information to initiate an IRIS payment transaction.
     *
     * @return $this
     */
    public function setIrisPaymentRequestUrl(string $irisPaymentRequestUrl): \Cardlink\Payments\Settings\RouteSettings
    {
        $this->irisPaymentRequestUrl = trim($irisPaymentRequestUrl);

        return $this;
    }

    /**
     * Get the URL to redirect the customer to after a successful IRIS payment transaction.
     *
     * @return string
     */
    public function getIrisPaymentSuccessUrl()
    {
        return $this->irisPaymentSuccessUrl;
    }

    /**
     * Set the URL to redirect the customer to after a successful IRIS payment transaction.
     *
     * @param string $irisPaymentSuccessUrl The URL to redirect the customer to after a successful IRIS payment transaction.
     *
     * @return $this
     */
    public function setIrisPaymentSuccessUrl(string $irisPaymentSuccessUrl)
    {
        $this->irisPaymentSuccessUrl = $irisPaymentSuccessUrl;

        return $this;
    }

    /**
     * Get the URL to redirect the customer to after a failed IRIS payment transaction.
     *
     * @return string
     */
    public function getIrisPaymentFailedUrl()
    {
        return $this->irisPaymentFailedUrl;
    }

    /**
     * Set the URL to redirect the customer to after a failed IRIS payment transaction.
     *
     * @param string $irisPaymentFailedUrl The URL to redirect the customer to after a failed IRIS payment transaction.
     *
     * @return $this
     */
    public function setIrisPaymentFailedUrl(string $irisPaymentFailedUrl)
    {
        $this->irisPaymentFailedUrl = $irisPaymentFailedUrl;

        return $this;
    }

    /**
     * Get the URL that the mobile clients will send customer and order information to initiate a PayPal payment transaction.
     *
     * @return string
     */
    public function getPayPalPaymentRequestUrl(): string
    {
        return $this->paypalPaymentRequestUrl;
    }

    /**
     * Set the URL that the mobile clients will send customer and order information to initiate a PayPal payment transaction.
     *
     * @param string $paymentRequestUrl The URL that the mobile clients will send customer and order information to initiate a PayPal payment transaction.
     *
     * @return $this
     */
    public function setPayPalPaymentRequestUrl(string $paypalPaymentRequestUrl): \Cardlink\Payments\Settings\RouteSettings
    {
        $this->paypalPaymentRequestUrl = trim($paypalPaymentRequestUrl);

        return $this;
    }

    /**
     * Get the URL to redirect the customer to after a successful PayPal payment transaction.
     *
     * @return string
     */
    public function getPayPalPaymentSuccessUrl()
    {
        return $this->paypalPaymentSuccessUrl;
    }

    /**
     * Set the URL to redirect the customer to after a successful PayPal payment transaction.
     *
     * @param string $payPalPaymentSuccessUrl The URL to redirect the customer to after a successful PayPal payment transaction.
     *
     * @return $this
     */
    public function setPayPalPaymentSuccessUrl(string $paypalPaymentSuccessUrl)
    {
        $this->paypalPaymentSuccessUrl = $paypalPaymentSuccessUrl;

        return $this;
    }

    /**
     * Get the URL to redirect the customer to after a failed PayPal payment transaction.
     *
     * @return string
     */
    public function getPayPalPaymentFailedUrl()
    {
        return $this->paypalPaymentFailedUrl;
    }

    /**
     * Set the URL to redirect the customer to after a failed PayPal payment transaction.
     *
     * @param string $payPalPaymentFailedUrl The URL to redirect the customer to after a failed PayPal payment transaction.
     *
     * @return $this
     */
    public function setPayPalPaymentFailedUrl(string $paypalPaymentFailedUrl)
    {
        $this->paypalPaymentFailedUrl = $paypalPaymentFailedUrl;

        return $this;
    }

    /**
     * Get the URL of the custom UI stylesheets.
     * 
     * @return string
     */
    public function getUiStyleSheetsUrl()
    {
        return trim($this->uiStyleSheetsUrl);
    }

    /**
     * Set the URL of the custom UI stylesheets.
     *
     * @param string $uiStyleSheetsUrl The URL of the custom UI stylesheets code (CSS).
     *
     * @return $this
     */
    public function setUiStyleSheetsUrl(string $uiStyleSheetsUrl)
    {
        $this->uiStyleSheetsUrl = $uiStyleSheetsUrl;

        return $this;
    }

}