<?php

namespace Cardlink\Payments\Models;

/**
 * Model of card tokenization data.
 * 
 * @package Cardlink\Payments\Models
 */
class CardToken implements \JsonSerializable
{
    /**
     * The tokenization string.
     * 
     * @var string
     */
    public string $token = '';

    /**
     * The last four digits of the tokenized card.
     * 
     * @var string
     */
    public string $last4Digits = '';

    /**
     * The expiration year of the tokenized card.
     * 
     * @var int
     */
    public int $expiryYear = 0;

    /**
     * The expiration month of the tokenized card.
     * 
     * @var int
     */
    public int $expiryMonth = 0;

    /**
     * The type of the tokenized card (visa/mastercard/)
     * 
     * @var string
     */
    public string $cardType = '';

    /**
     * @param array|null $initData Initialization array containing the tokenization data.
     */
    public function __construct(array $initData = null)
    {
        if (isset($initData)) {
            $this->token = $initData['token'];
            $this->cardType = $initData['card_type'];
            $this->last4Digits = $initData['last4'];
            $this->expiryYear = intval($initData['expiry_year']);
            $this->expiryMonth = intval($initData['expiry_month']);
        }
    }

    /**
     * Method called automatically to transform the object data that will be encoded to JSON string.
     * 
     * @return array A standardized array format for the tokenized card.
     */
    public function jsonSerialize()
    {
        return [
            'token' => $this->token,
            'card_type' => $this->cardType,
            'last4' => $this->last4Digits,
            'expiry_year' => intval($this->expiryYear),
            'expiry_month' => intval($this->expiryMonth)
        ];
    }

    /**
     * Load the object directly with data from the payment gateway.
     * 
     * @param string $cardType The type of the tokenized card.
     * @param array $data The card tokenization information from the payment gateway.
     * @return $this
     */
    public function loadPaymentGatewayData(string $cardType, array $data): \Cardlink\Payments\Models\CardToken
    {
        $this->cardType = trim(strip_tags(!is_numeric($cardType) ? $cardType : \Cardlink\Payments\Helpers\Card::getCardTypeFromId($cardType)));
        $this->token = $data['ExtToken'];
        $this->last4Digits = $data['ExtTokenPanEnd'];
        $extTokenExp = $data['ExtTokenExp'];

        $this->expiryYear = intval(substr($extTokenExp, 0, 4));
        $this->expiryMonth = intval(substr($extTokenExp, 5, 2));

        return $this;
    }
}