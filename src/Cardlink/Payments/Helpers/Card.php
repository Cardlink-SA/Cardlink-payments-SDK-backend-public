<?php

namespace Cardlink\Payments\Helpers;

/**
 * Collection of helper methods for handling credit/debit cards.
 * 
 * @package Cardlink\Payments\Helpers
 */
class Card
{
    /**
     * Get the card type from the card's Personal Account Number (PAN).
     * 
     * @param string $pan The card's Personal Account Number, i.e. the card's number.
     * 
     * @return string The card type.
     */
    public static function getCardTypeFromPAN(string $pan): string
    {
        $card_types = [
            "visa" => "/^4[0-9]{12}(?:[0-9]{3})?$/",
            "mastercard" => "/^5[1-5][0-9]{14}$/",
            "amex" => "/^3[47][0-9]{13}$/",
            "discover" => "/^6(?:011|5[0-9]{2})[0-9]{12}$/",
        ];

        foreach ($card_types as $cardType => $cardRegEx) {
            if (preg_match($cardRegEx, $pan)) {
                return $cardType;
            }
        }
        return null;
    }

    /**
     * Get the card type from a predefined look-up table.
     * 
     * @param string $pan The ID of the card type.
     * 
     * @return string The card type.
     */
    public static function getCardTypeFromId(string $cardTypeId): string
    {
        switch ($cardTypeId) {
            case '1':
                return 'visa';

            case '2':
                return 'mastercard';

            case '3':
                return 'maestro';

            case '4':
                return 'amex';

            case '5':
                return 'diners';

            case '6':
                return 'discover';
        }
        return null;
    }
}