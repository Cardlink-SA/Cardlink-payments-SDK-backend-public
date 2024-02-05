<?php

namespace Cardlink\Payments\Models;

class InstallmentsVariation implements \JsonSerializable
{
    /**
     * The order amount up to which the number of installments will apply.
     *
     * @var int
     */
    public int $amount = 0;

    /**
     * The maximum number of installments that the customer will be able to select.
     *
     * @var int
     */
    public int $installments = 0;

    /**
     * @param array|null $initData Initialization data array.
     */
    public function __construct(array $initData = null)
    {
        if (isset($initData)) {
            $this->amount = intval($initData['amount']);
            $this->installments = intval($initData['installments']);
        }
    }

    /**
     * Method called automatically to transform the object data that will be encoded to JSON string.
     * 
     * @return array A standardized array format for the installments variation object.
     */
    public function jsonSerialize()
    {
        return [
            'amount' => $this->amount,
            'installments' => $this->installments,
        ];
    }
}