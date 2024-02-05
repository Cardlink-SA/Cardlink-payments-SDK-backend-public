<?php

namespace Cardlink\Payments\Settings;

/**
 * Class PaymentSettings
 * 
 * @package Cardlink\Payments\Settings
 */
class PaymentSettings
{
    /**
     * Identifies that payment transactions are executed under the production or staging environment.
     * 
     * @var bool
     */
    private $is_production = false;

    /**
     * The currency code of the transactions.
     * 
     * @var string
     */
    private string $currency = 'EUR';

    /**
     * Identifies that the transaction should execute a CAPTURE (TRUE) or AUTHORIZE (FALSE).
     * 
     * @var bool
     */
    private bool $captureMode = true;

    /**
     * The maximum number of installments when not employing installments variations.
     * 
     * @var int
     */
    private int $max_installments = 0;

    /**
     * Installments variations - objects that instruct the checkout process on the maximum number of installments the
     * customer can select depending on the order amount.
     * 
     * @var array
     */
    private array $installments_variations = [];

    /**
     * Status flag indicating whether the payment gateway will allow card tokenization on the checkout.
     * 
     * @var bool
     */
    private bool $allows_tokenization = false;

    /**
     * The name of the acquirer service.
     * 
     * @var int
     */
    private string $acquirer = \Cardlink\Payments\Constants\Acquirer::CARDLINK;

    /**
     * List of accepted card types.
     * 
     * @var array
     */
    private array $accepted_card_types = [];

    /**
     * List of accepted payment methods.
     * 
     * @var array
     */
    private array $accepted_payment_methods = [];

    /**
     * Get the payment transaction execution environment (production/staging).
     *
     * @return  bool
     */
    public function isProduction(): bool
    {
        return $this->is_production;
    }

    /**
     * Set the payment transaction execution environment (production/staging).
     *
     * @param  bool  $is_production  Identifies that payment transactions are executed under the production or staging environment.
     * @return  $this
     */
    public function setIsProduction(bool $is_production): \Cardlink\Payments\Settings\PaymentSettings
    {
        $this->is_production = $is_production;

        return $this;
    }

    /**
     * Set the currency code of the transactions.
     * 
     * @param string $currency The 
     * @return  $this
     */
    public function setCurrency(string $currency): \Cardlink\Payments\Settings\PaymentSettings
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the currency code of the transactions.
     * 
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Set the transaction mode. 
     * 
     * @param bool $captureMode True for CAPTURE, False for AUTHORIZE. 
     * @return  $this
     */
    public function setCaptureMode(bool $captureMode): \Cardlink\Payments\Settings\PaymentSettings
    {
        $this->captureMode = $captureMode;

        return $this;
    }

    /**
     * Get the currently configured transaction mode.
     * 
     * @return bool True for CAPTURE, False for AUTHORIZE.
     */
    public function getCaptureMode(): bool
    {
        return $this->captureMode;
    }

    /**
     * Set whether the payment gateway will allow card tokenization.
     * 
     * @param bool $allows_tokenization A boolean value indicating whether card tokenization is allowed.
     * @return $this
     */
    public function setAllowsTokenization(bool $allows_tokenization): \Cardlink\Payments\Settings\PaymentSettings
    {
        $this->allows_tokenization = $allows_tokenization;

        return $this;
    }

    /**
     * Identify whether the payment gateway will allow card tokenization.
     * 
     * @return bool A boolean value indicating whether card tokenization is allowed.
     */
    public function allowsTokenization(): bool
    {
        return $this->allows_tokenization;
    }

    public function setInstallmentsVariations($installments_variations): \Cardlink\Payments\Settings\PaymentSettings
    {
        if (!is_array($installments_variations)) {
            $installments_variations = [];
        }
        $this->installments_variations = $installments_variations;

        return $this;
    }

    /**
     * Returns the configured array of installments variations.
     * 
     * @return array
     */
    public function getInstallmentsVariations(): array
    {
        return $this->installments_variations;
    }

    /**
     * Returns a sanitized (clean) version of the configured array of installments variations.
     * Variations with invalid settings are filtered out.
     * 
     * @return array
     */
    public function getSanitizedInstallmentsVariations(): array
    {
        $ret = [];

        foreach ($this->installments_variations as $variation) {
            $amount = max(0, intval($variation->amount));
            // Set upper (60) and lower (0) limit of the number of installments.
            $installments = min(60, max(0, intval($variation->installments)));

            if ($installments <= 1) {
                $installments = 0;
            }

            if ($amount > 0) {
                $ret[] = [
                    'amount' => $amount,
                    'installments' => $installments
                ];
            }
        }

        return $ret;
    }

    /**
     * Add an installments variation.
     * 
     * @param \Cardlink\Payments\Models\InstallmentsVariation $installments_variation
     * @return bool Returns TRUE if the input value was successfuly added.
     */
    public function addInstallmentsVariation(\Cardlink\Payments\Models\InstallmentsVariation $installments_variation)
    {
        $ret = false;

        if (get_class($installments_variation) == \Cardlink\Payments\Models\InstallmentsVariation::class) {
            $this->installments_variations[] = $installments_variation;
            $ret = true;
        }

        return $ret;
    }

    /**
     * Set the value of the acquirer.
     *
     * @param string $acquirer 
     * @return $this
     */
    public function setAcquirer(string $acquirer): \Cardlink\Payments\Settings\PaymentSettings
    {
        $this->acquirer = trim(strtolower($acquirer));

        return $this;
    }

    /**
     * Get the value/name of the acquirer
     * 
     * @return string
     */
    public function getAcquirer(): string
    {
        return $this->acquirer;
    }

    /**
     * Set the list of the accepted payment methods.
     *
     * @param array $accepted_payment_methods 
     * @return $this
     */
    public function setAcceptedPaymentMethods(array $accepted_payment_methods): \Cardlink\Payments\Settings\PaymentSettings
    {
        $this->accepted_payment_methods = $accepted_payment_methods;

        return $this;
    }

    /**
     * Get the list of the accepted credit/debit card types.
     * 
     * @return array
     */
    public function getAcceptedPaymentMethods(): array
    {
        return $this->accepted_payment_methods;
    }

    /**
     * Set the list of the accepted credit/debit card types.
     *
     * @param array $accepted_card_types 
     * @return $this
     */
    public function setAcceptedCardTypes(array $accepted_card_types): \Cardlink\Payments\Settings\PaymentSettings
    {
        $this->accepted_card_types = $accepted_card_types;

        return $this;
    }

    /**
     * Get the list of the accepted credit/debit card types.
     * 
     * @return array
     */
    public function getAcceptedCardTypes(): array
    {
        return $this->accepted_card_types;
    }

    /**
     * Get the value of the maximum number of installments.
     * 
     * @return int
     */
    public function getMaxInstallments(): int
    {
        return $this->max_installments;
    }

    /**
     * Set the value of the maximum number of installments.
     *
     * @return $this
     */
    public function setMaxInstallments(int $max_installments): \Cardlink\Payments\Settings\PaymentSettings
    {
        $this->max_installments = max(0, min(60, intval($max_installments)));

        return $this;
    }
}