<?php

namespace Cardlink\Payments\Settings;

/**
 * Data model for storing merchant and processor settings.
 * 
 * @package Cardlink\Payments\Settings
 */
class MerchantSettings
{
    /**
     * The Merchant ID (provided by Cardlink).
     * 
     * @var string
     */
    private $merchant_id;

    /**
     * The Shared Secret Key (provided by Cardlink).
     * 
     * @var string
     */
    private $shared_secret;

    /**
     * The merchant's private key (PKCS8 format).
     * 
     * @var string
     */
    private $merchant_private_key;

    /**
     * The processor's certificate (X509 format).
     * 
     * @var string
     */
    private $processor_certificate;

    /**
     * The customer code for DIAS.
     * 
     * @var string
     */
    private $dias_customer_code;

    /**
     * Set the Merchant ID.
     * 
     * @param string $merchant_id The Merchant ID that was provided by Cardlink.
     * @return $this
     */
    public function setMerchantId(string $merchant_id): \Cardlink\Payments\Settings\MerchantSettings
    {
        $this->merchant_id = trim($merchant_id);

        return $this;
    }

    /**
     * Get the Merchant ID.
     * 
     * @return string The Merchant ID that was provided by Cardlink.
     */
    public function getMerchantId(): string
    {
        return $this->merchant_id;
    }

    /**
     * Set the Shared Secret Key used to create and verify message digests.
     * 
     * @param string $shared_secret The Shared Secret that was provided by Cardlink.
     * @return $this
     */
    public function setSharedSecret(string $shared_secret): \Cardlink\Payments\Settings\MerchantSettings
    {
        $this->shared_secret = trim($shared_secret);

        return $this;
    }

    /**
     * Get the Shared Secret Key used to create and verify message digests.
     * 
     * @return string The Shared Secret that was provided by Cardlink.
     */
    public function getSharedSecret(): string
    {
        return $this->shared_secret;
    }

    /**
     * Set the merchant's private key used to sign messages sent to the processor.
     * 
     * @param string $shared_secret The merchant's private key in PKCS8 format.
     * @return $this
     */
    public function setMerchantPrivateKey(string $merchant_private_key): \Cardlink\Payments\Settings\MerchantSettings
    {
        $this->merchant_private_key = str_replace(["\r", "\n"], "", trim($merchant_private_key));

        return $this;
    }

    /**
     * Get the merchant's private key used to sign messages sent to the processor.
     * 
     * @return string The merchant's private key in PKCS8 format.
     */
    public function getMerchantPrivateKey(): string
    {
        if (!\Cardlink\Payments\Helpers\Tools::startsWith($this->merchant_private_key, '-----BEGIN ')) {
            $this->merchant_private_key = "-----BEGIN PRIVATE KEY-----\n" . $this->merchant_private_key . "\n-----END PRIVATE KEY-----";
        }
        return $this->merchant_private_key;
    }

    /**
     * Set the processor's certificate.
     * 
     * @param string $processor_certificate The processor's certificate in X509 format.
     * @return $this
     */
    public function setProcessorCertificate(string $processor_certificate): \Cardlink\Payments\Settings\MerchantSettings
    {
        $this->processor_certificate = str_replace("\r", "", trim($processor_certificate));

        return $this;
    }

    /**
     * Get the processor's certificate.
     * 
     * @return string The processor's certificate in X509 format.
     */
    public function getProcessorCertificate(): string
    {
        if (!\Cardlink\Payments\Helpers\Tools::startsWith($this->processor_certificate, '-----BEGIN ')) {
            $this->processor_certificate = "-----BEGIN CERTIFICATE-----\n" . $this->processor_certificate . "\n-----END CERTIFICATE-----";
        }

        return $this->processor_certificate;
    }

    /**
     * Set the DIAS customer code.
     * 
     * @param string|int $diasCode The customer code for DIAS.
     * @return $this
     */
    public function setDiasCustomerCode(string $diasCode): \Cardlink\Payments\Settings\MerchantSettings
    {
        $this->dias_customer_code = trim($diasCode);

        return $this;
    }

    /**
     * Get the DIAS customer code.
     * 
     * @return string The customer code for DIAS.
     */
    public function getDiasCustomerCode(): string
    {
        return $this->dias_customer_code;
    }

}