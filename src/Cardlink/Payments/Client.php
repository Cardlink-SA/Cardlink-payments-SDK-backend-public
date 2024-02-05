<?php

namespace Cardlink\Payments;

use DateTime;
use DateTimeZone;
use GuzzleHttp;
use Alcohol;
use League;

class Client
{
    /**
     * Determines whether the client runs in debug mode or not.
     * 
     * @var bool
     */
    private bool $debugMode = false;

    /**
     * Holds the URL of the custom CSS file to be used in IRIS or PayPal transactions.
     * 
     * @var string | null
     */
    private ?string $customCssUrl = null;

    private int $defaultLogFacility = 4;

    private object $iso4217;
    private object $iso3166;

    /**
     * @var DateTimeZone
     */
    private DateTimeZone $timezone;

    /**
     * Route related settings.
     * 
     * @var \Cardlink\Payments\Settings\RouteSettings
     */
    public \Cardlink\Payments\Settings\RouteSettings $RouteSettings;

    /**
     * Merchant related settings.
     * 
     * @var \Cardlink\Payments\Settings\MerchantSettings
     */
    public \Cardlink\Payments\Settings\MerchantSettings $MerchantSettings;

    /**
     * Payment related settings.
     * 
     * @var \Cardlink\Payments\Settings\PaymentSettings
     */
    public \Cardlink\Payments\Settings\PaymentSettings $PaymentSettings;

    function __construct()
    {
        $this->iso4217 = new Alcohol\ISO4217();
        $this->iso3166 = new League\ISO3166\ISO3166();
        $this->RouteSettings = new \Cardlink\Payments\Settings\RouteSettings();
        $this->MerchantSettings = new \Cardlink\Payments\Settings\MerchantSettings();
        $this->PaymentSettings = new \Cardlink\Payments\Settings\PaymentSettings();
    }

    /**
     * Get the value of debugMode.
     */
    public function isDebugMode()
    {
        return $this->debugMode;
    }

    /**
     * Set the value of debugMode.
     *
     * @return $this
     */
    public function setDebugMode($debugMode): Client
    {
        $this->debugMode = $debugMode;

        return $this;
    }

    /**
     * Set the value of timezone.
     *
     * @param  string  $timezone
     *
     * @return  $this
     */
    public function setTimezone(string $timezone)
    {
        $this->timezone = new DateTimeZone($timezone);

        return $this;
    }

    /**
     * Set the URL of the custom CSS file to be used for IRIS and PayPal transactions.
     * If null or an invalid URL is set, the default CSS files for the SDK will be used.
     *
     * @param  string  $customCssUrl The URL of the custom CSS file.
     *
     * @return  $this
     */
    public function setCustomCssUrl(string $customCssUrl)
    {
        $this->customCssUrl = filter_var($customCssUrl, FILTER_SANITIZE_URL);

        return $this;
    }

    /**
     * Get the payment method settings response data object.
     * 
     * @return array
     */
    public function getPaymentSettingsResponse(): array
    {
        $max_installments = $this->PaymentSettings->getMaxInstallments();
        $installments_variations = $this->PaymentSettings->getSanitizedInstallmentsVariations();

        if (count($installments_variations) || $max_installments > 1) {
            $installments = true;
        } else {
            $installments = false;
        }

        $response = [
            'status' => 200,
            'message' => 'OK',
            'settings' => [
                'currency' => $this->PaymentSettings->getCurrency(),
                'accepted_card_types' => $this->PaymentSettings->getAcceptedCardTypes(),
                'accepted_payment_methods' => $this->PaymentSettings->getAcceptedPaymentMethods(),
                'installments' => $installments,
                'max_installments' => $max_installments,
                'installments_variations' => $installments_variations,
                'tokenization' => $this->PaymentSettings->allowsTokenization(),
                'acquirer' => $this->PaymentSettings->getAcquirer(),
                'routes' => [
                    'card_payment_request' => $this->RouteSettings->getPaymentRequestUrl(),
                    'card_payment_success' => $this->RouteSettings->getPaymentSuccessUrl(),
                    'card_payment_failed' => $this->RouteSettings->getPaymentFailedUrl(),
                    'iris_payment_request' => $this->RouteSettings->getIrisPaymentRequestUrl(),
                    'iris_payment_success' => $this->RouteSettings->getIrisPaymentSuccessUrl(),
                    'iris_payment_failed' => $this->RouteSettings->getIrisPaymentFailedUrl(),
                    'paypal_payment_request' => $this->RouteSettings->getPayPalPaymentRequestUrl(),
                    'paypal_payment_success' => $this->RouteSettings->getPayPalPaymentSuccessUrl(),
                    'paypal_payment_failed' => $this->RouteSettings->getPayPalPaymentFailedUrl(),
                ]
            ]
        ];

        return $response;
    }

    /**
     * Send an authentication request to the authentication server.
     * 
     * @param array $clientRequestData The client request data 
     * @return array
     */
    public function sendAuthenticationRequest(array $clientRequestData): array
    {
        $order_id = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'order_id', null);
        $cardType = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'cardType', '');
        $pan = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'pan', '');
        $expiry = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'expiry', '');
        $cvv = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'cvv', '');
        $cardEncData = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'cardEncData', '');
        $purchAmount = intval(\Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'purchAmount', ''));
        $description = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'description', '');
        $currency = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'currency', '978');
        $cardholderEmail = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'TDS2CardholderEmail', '');
        $cardholderName = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'TDS2CardholderName', '');
        $billAddrCity = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'TDS2BillAddrCity', '');
        $billAddrLine1 = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'TDS2BillAddrLine1', '');
        $billAddrCountry = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'TDS2BillAddrCountry', '300');
        $billAddrPostCode = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'TDS2BillAddrPostCode', '');
        $recurFreq = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'recurFreq', '');
        $recurEnd = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'recurEnd', '');
        $installments = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'installments', '');
        $extTokenOptions = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'extTokenOptions', '');
        $extToken = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'extToken', '');
        $deviceCategory = \Cardlink\Payments\Helpers\Tools::extractFromArray($clientRequestData, 'deviceCategory', '0');

        if ($order_id == null || $order_id == '') {
            $order_id = \Cardlink\Payments\Helpers\Crypto::generateRandomString(20);
        }

        $orderCurrency = (is_numeric($currency)) ? $this->iso4217->getByNumeric($currency) : $this->iso4217->getByAlpha3($currency);
        $currencyNum = $orderCurrency['numeric'];
        $currencyAlpha3 = $orderCurrency['alpha3'];

        $orderBillingAddrCountry = (is_numeric($billAddrCountry)) ? $this->iso3166->numeric($billAddrCountry) : $this->iso3166->alpha2($billAddrCountry);
        $billAddrCountryNum = $orderBillingAddrCountry['numeric'];
        $billAddrCountryAlpha2 = $orderBillingAddrCountry['alpha2'];

        $form_data_array = [
            'version' => '4.0',
            'pan' => $pan,
            'expiry' => $expiry,
            'cardEncData' => $cardEncData,
            'deviceCategory' => $deviceCategory ? $deviceCategory : '0',
            'purchAmount' => $purchAmount,
            'exponent' => '2',
            'description' => $description,
            // ISO4217 numeric
            'currency' => $currencyNum,
            'merchantID' => $this->MerchantSettings->getMerchantId(),
            'xid' => base64_encode(\Cardlink\Payments\Helpers\Crypto::generateRandomString(20)),
            'merchantTxId' => \Cardlink\Payments\Helpers\Crypto::generateRandomString(40),
            'okUrl' => $this->RouteSettings->getPaymentSuccessUrl(),
            'failUrl' => $this->RouteSettings->getPaymentFailedUrl(),
        ];

        if ($this->isDebugMode()) {
            error_log(var_export($form_data_array, true), $this->defaultLogFacility);
        }

        $tds2 = [
            'TDS2.cardholderName' => $cardholderName,
            'TDS2.email' => $cardholderEmail,
            'TDS2.billAddrCity' => $billAddrCity,
            'TDS2.billAddrCountry' => $billAddrCountryNum,
            'TDS2.billAddrLine1' => $billAddrLine1,
            'TDS2.billAddrPostCode' => $billAddrPostCode,
        ];

        $transient_data = [
            'order_id' => $order_id,
            'pan' => $form_data_array['pan'],
            'cardType' => ($cardType != '') ? $cardType : \Cardlink\Payments\Helpers\Card::getCardTypeFromPAN($form_data_array['pan']),
            'expiry' => $form_data_array['expiry'],
            'cvv' => $cvv,
            'cardEncData' => $form_data_array['cardEncData'],
            'purchAmount' => $form_data_array['purchAmount'],
            // ISO4217 alpha3
            'currency' => $currencyAlpha3,
            'description' => $form_data_array['description'],
            'cardholderName' => $tds2['TDS2.cardholderName'],
            'cardholderEmail' => $tds2['TDS2.email'],
            'billCity' => $tds2['TDS2.billAddrCity'],
            // ISO3166 numeric
            'billCountryNum' => $billAddrCountryNum,
            // ISO3166 alpha2
            'billCountryAlpha2' => $billAddrCountryAlpha2,
            'billLine1' => $tds2['TDS2.billAddrLine1'],
            'billPostCode' => $tds2['TDS2.billAddrPostCode'],
            'recurFreq' => $recurFreq,
            'recurEnd' => $recurEnd,
            'installments' => $installments,
            'extTokenOptions' => $extTokenOptions,
            'extToken' => $extToken,
        ];

        if (isset($extToken) && $extToken !== '') {
            $transient_data['extTokenOptions'] = '110';
        }

        $merchant_data = base64_encode(json_encode($transient_data));
        $merchant_data_key = base64_encode(\Cardlink\Payments\Helpers\Crypto::generateGuid());

        if ($this->isDebugMode()) {
            error_log('merchant_data_key: ' . $merchant_data_key, $this->defaultLogFacility);
        }

        $form_data_array['MD'] = $merchant_data_key;

        if ($recurFreq && $recurEnd) {
            $form_data_array['recurFreq'] = $recurFreq;
            $form_data_array['recurEnd'] = $recurEnd;
        } else if (isset($installments) && $installments !== '') {
            $form_data_array['installments'] = $installments;
        }

        // tokenized transaction
        if (isset($extToken) && $extToken !== '') {
            $form_data_array['pan'] = $extToken;
            $form_data_array['panMode'] = 'VPOSToken';
        }

        $merged_data = array_merge($form_data_array, $tds2);
        $merged_data['signature'] = \Cardlink\Payments\Helpers\Crypto::calculateSignature($merged_data, $this->MerchantSettings->getMerchantPrivateKey());

        $httpClient = new GuzzleHttp\Client([
            'timeout' => 120,
            'connect_timeout' => 45,
            'allow_redirects' => true,
            'debug' => false,
            'synchronous' => true,
            'version' => 1.1 // HTTP 1.1
        ]);

        switch ($this->PaymentSettings->getAcquirer()) {
            case \Cardlink\Payments\Constants\Acquirer::NEXI:
                $mpiUrl = ($this->PaymentSettings->isProduction())
                    ? 'https://www.alphaecommerce.gr/mdpaympi/MerchantServer'
                    : 'https://alphaecommerce-test.cardlink.gr/mdpaympi/MerchantServer';
                break;

            case \Cardlink\Payments\Constants\Acquirer::WORLDLINE:
                $mpiUrl = ($this->PaymentSettings->isProduction())
                    ? 'https://vpos.eurocommerce.gr/mdpaympi/MerchantServer'
                    : 'https://eurocommerce-test.cardlink.gr/mdpaympi/MerchantServer';
                break;

            case \Cardlink\Payments\Constants\Acquirer::CARDLINK:
            default:
                $mpiUrl = ($this->PaymentSettings->isProduction())
                    ? 'https://ecommerce.cardlink.gr/mdpaympi/MerchantServer'
                    : 'https://ecommerce-test.cardlink.gr/mdpaympi/MerchantServer';
                break;
        }

        $response = $httpClient->post($mpiUrl, [
            'form_params' => $merged_data
        ]);

        $data = [
            'status' => 200,
            'message' => '',
            'body' => null
        ];

        if (!$response->getStatusCode() == 200) {
            $data['status'] = 400;
            $data['message'] = $response->getReasonPhrase();
        } else {
            $responseData = $response->getBody()->getContents();
            $data['status'] = $response->getStatusCode();
            $data['message'] = $response->getReasonPhrase();
            $data['body'] = $responseData;
        }

        if ($this->isDebugMode()) {
            error_log('3D secure response:' . PHP_EOL . json_encode($data), $this->defaultLogFacility);
        }

        return [
            'status' => $data['status'],
            'merchant_data' => $merchant_data,
            'merchant_data_key' => $merchant_data_key,
            'merged_data' => $merged_data,
            'response' => $data
        ];
    }

    /**
     * Method to process the response of the authentication server.
     * 
     * @param array $authenticationServerResponseData
     * @param string|null $encodedMerchantData
     * @param callable|null $storeCardTokenCallback
     * @return array
     */
    public function processAuthenticationServerResponse(
        array $authenticationServerResponseData,
        ?string $encodedMerchantData,
        callable $storeCardTokenCallback = null
    ): array {

        $acceptedStatusCodes = ['1', '4'];

        if (!in_array($authenticationServerResponseData['mdStatus'], $acceptedStatusCodes, false)) {
            if ($this->isDebugMode()) {
                error_log('payment response with error', $authenticationServerResponseData['mdStatus'] . ' ' . $authenticationServerResponseData['mdErrorMsg']);
            }

            return [
                'status' => 400,
                'message' => $authenticationServerResponseData['mdStatus'] . ' ' . $authenticationServerResponseData['mdErrorMsg']
            ];
        }

        // Data keys in defined order to use for verification of message authenticity.
        $dataKeys = [
            'version',
            'merchantID',
            'xid',
            'merchantTxId',
            'mdStatus',
            'mdErrorMsg',
            'veresEnrolledStatus',
            'paresTxStatus',
            'iReqCode',
            'iReqDetail',
            'vendorCode',
            'eci',
            'cavv',
            'cavvAlgorithm',
            'MD',
            'PAResVerified',
            'PAResSyntaxOK',
            'protocol',
            'cardType',
            'fssScore',
            'TDS2_transStatus',
            'TDS2_transStatusReason',
            'TDS2_threeDSServerTransID',
            'TDS2_dsTransID',
            'TDS2_acsTransID',
            'TDS2_acsRenderingType',
            'TDS2_acsReferenceNumber',
            'TDS2_acsSignedContent',
            'TDS2_authTimestamp',
            'TDS2_messageVersion',
            'TDS2_acsChallengeMandated',
            'TDS2_authenticationType',
            'TDS2_acsOperatorID',
            'TDS2_cardholderInfo',
            'TDS2_acsUrl',
            'TDS2_challengeCancel',
            'TDS2_AResExtensions',
            'TDS2_RReqExtensions',
            'signature'
        ];

        $authentication_data = [];
        $digestable_data = [];

        foreach ($dataKeys as $dataKey) {
            if (array_key_exists($dataKey, $authenticationServerResponseData)) {
                $authentication_data[$dataKey] = $authenticationServerResponseData[$dataKey];

                if ($dataKey != 'signature') {
                    $digestable_data[] = $authentication_data[$dataKey];
                }
            }
        }

        $is_valid_signature = \Cardlink\Payments\Helpers\Crypto::validateSignature(
            $digestable_data,
            $authentication_data['signature'],
            $this->MerchantSettings->getProcessorCertificate()
        );

        if (!$is_valid_signature) {
            if ($this->isDebugMode()) {
                error_log('Invalid signature in response data');
            }

            return [
                'status' => 400,
                'message' => 'Invalid signature'
            ];
        }

        if ($this->isDebugMode()) {
            error_log('payment response', $this->defaultLogFacility);
        }

        $authentication_data['cardType'] = $authentication_data['cardType'] != null ? \Cardlink\Payments\Helpers\Card::getCardTypeFromId($authentication_data['cardType']) : '';

        $merchant_data = json_decode(base64_decode($encodedMerchantData), true);

        if ($this->isDebugMode()) {
            error_log('Get stored data', $this->defaultLogFacility);
            error_log(json_encode($merchant_data), $this->defaultLogFacility);
            error_log('modirum response data', $this->defaultLogFacility);
            error_log(json_encode($authentication_data), $this->defaultLogFacility);
        }

        switch ($authentication_data['mdStatus']) {
            case '1': // Fully Authenticated, continue transaction
            case '4': // Attempt (Proof of authentication attempt, may continue to transaction)
                return $this->sendPaymentRequestToModirum($authentication_data, $merchant_data, $storeCardTokenCallback);

            default:
                return [
                    'status' => 400,
                    'message' => 'Payment stopped due to mdStatus=' . $authentication_data['mdStatus']
                ];
        }
    }

    /**
     * Send the payment request to the payment gateway on a server-to-server communications channel.
     * 
     * @param array $authentication_data Data from the authentication server.
     * @param array $merchant_data Previously stored information for the order.
     * @param callable|null $storeCardTokenCallback If card tokenization is allowed, this callback can be used to store the token on the ecommerce platform.
     * @return array
     */
    private function sendPaymentRequestToModirum(
        array $authentication_data,
        array $merchant_data,
        callable $storeCardTokenCallback = null
    ): array {
        $order_id = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'order_id', null);

        if ($order_id == null || $order_id == '') {
            $order_id = \Cardlink\Payments\Helpers\Crypto::generateRandomString(20);
        }

        $order_id = strtolower($order_id);
        $orderDesc = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'description', '');
        $orderAmount = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'purchAmount', '');
        $orderAmount = substr_replace($orderAmount, '.', (strlen($orderAmount) - 2), 0);
        $currencyAlpha3 = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'currency', 'EUR'); // ISO4217 alpha3
        $pan = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'pan', '');
        $cardEncData = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'cardEncData');
        $payMethod = \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'cardType');
        $cardExpDate = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'expiry');
        $cvv = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'cvv');
        $cardholderName = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'cardholderName', '');
        $cardholderEmail = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'cardholderEmail', '');
        $recurFreq = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'recurFreq');
        $recurEnd = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'recurEnd');
        $installments = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'installments');
        $extTokenOptions = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'extTokenOptions');
        $extToken = \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'extToken');

        $form_data_array = [
            'orderid' => $order_id . 'at' . date('YmdHis'),
            'orderDesc' => $orderDesc,
            'orderAmount' => $orderAmount,
            // ISO4217 alpha3
            'currency' => $currencyAlpha3,
            'payerEmail' => $cardholderEmail,
            'payMethod' => $payMethod,
            'cardPan' => $pan,
            'cardExpDate' => $cardExpDate,
            'cardCvv2' => $cvv,
            'cardHolderName' => $cardholderName,
            'extTokenOptions' => $extTokenOptions,
            'extToken' => $extToken,
        ];

        $billing = [
            // ISO 3166 alpha2
            'country' => \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'billCountryAlpha2'),
            'city' => \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'billCity'),
            'zip' => \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'billPostCode'),
            'address' => \Cardlink\Payments\Helpers\Tools::extractFromArray($merchant_data, 'billLine1'),
        ];

        $xmlns_url = 'http://www.modirum.com/schemas/vposxmlapi41';
        $xmlns_ns2_url = 'http://www.w3.org/2000/09/xmldsig#';
        $version = '2.1';

        $billing_address = '<BillingAddress>'
            . '<country>' . $billing['country'] . '</country>'
            . '<city>' . $billing['city'] . '</city>'
            . '<zip>' . $billing['zip'] . '</zip>'
            . '<address>' . $billing['address'] . '</address>'
            . '</BillingAddress>';

        $three_ds_secure = '';
        $tokens = '';

        if (isset($extTokenOptions)) {
            if ($extTokenOptions !== '') {
                $tokens .= '<ExtTokenOptions>' . $form_data_array['extTokenOptions'] . '</ExtTokenOptions>';
            }

            if ($extTokenOptions == '110') {
                $tokens .= '<ExtToken>' . $form_data_array['extToken'] . '</ExtToken>';
            }
        }

        $three_ds_secure = '<ThreeDSecure>'
            . '<EnrollmentStatus>' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'veresEnrolledStatus') . '</EnrollmentStatus>'
            . '<AuthenticationStatus>' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'paresTxStatus') . '</AuthenticationStatus>'
            . '<CAVV>' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'cavv') . '</CAVV>'
            . '<XID>' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'xid') . '</XID>'
            . '<ECI>' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'eci') . '</ECI>'
            . '<Protocol>' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'protocol') . '</Protocol>'
            . '<Attribute name="TDS2.transStatus">' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'TDS2_transStatus') . '</Attribute>'
            . '<Attribute name="TDS2.threeDSServerTransID">' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'TDS2_threeDSServerTransID') . '</Attribute>'
            . '<Attribute name="TDS2.dsTransID">' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'TDS2_dsTransID') . '</Attribute>'
            . '<Attribute name="TDS2.acsTransID">' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'TDS2_acsTransID') . '</Attribute>'
            . '<Attribute name="TDS2.authenticationType">' . \Cardlink\Payments\Helpers\Tools::extractFromArray($authentication_data, 'TDS2_authenticationType') . '</Attribute>'
            . '</ThreeDSecure>';

        $order_info = '<OrderInfo>'
            . '<OrderId>' . $form_data_array['orderid'] . '</OrderId>'
            . '<OrderDesc>' . $form_data_array['orderDesc'] . '</OrderDesc>'
            . '<OrderAmount>' . $form_data_array['orderAmount'] . '</OrderAmount>'
            . '<Currency>' . $form_data_array['currency'] . '</Currency>'
            . '<PayerEmail>' . $form_data_array['payerEmail'] . '</PayerEmail>'
            . $billing_address .
            '</OrderInfo>';

        $recurring_xml = '';

        if ((isset($recurFreq) && $recurFreq != '') && (isset($recurEnd) && $recurEnd != '')) {
            $recurring_xml .= '<RecurringIndicator>R</RecurringIndicator>'
                . '<RecurringParameters>'
                . '<ExtRecurringfrequency>' . $recurFreq . '</ExtRecurringfrequency>'
                . '<ExtRecurringenddate>' . $recurEnd . '</ExtRecurringenddate>'
                . '</RecurringParameters>';
        }

        $installments_xml = '';

        if (isset($installments) && $installments != '') {
            $installments_xml .= '<InstallmentParameters>'
                . '<ExtInstallmentoffset>0</ExtInstallmentoffset>'
                . '<ExtInstallmentperiod>' . $installments . '</ExtInstallmentperiod>'
                . '</InstallmentParameters>';
        }

        $pay_method = $form_data_array['payMethod'];
        $pay_method_xml = '<PayMethod>' . $pay_method . '</PayMethod>';

        if (isset($cardEncData) && $cardEncData != '') {
            $card_fields = '<CardEncData>' . $cardEncData . '</CardEncData>';
        } else {
            $card_fields = '<CardPan>' . $form_data_array['cardPan'] . '</CardPan>'
                . '<CardExpDate>' . $form_data_array['cardExpDate'] . '</CardExpDate>'
                . '<CardCvv2>' . $form_data_array['cardCvv2'] . '</CardCvv2>'
                . '<CardHolderName>' . $form_data_array['cardHolderName'] . '</CardHolderName>';
        }

        $transaction_type = $this->PaymentSettings->getCaptureMode() ? 'SaleRequest' : 'AuthorisationRequest';

        $data_xml_inner = '<' . $transaction_type . '><Authentication>'
            . '<Mid>' . $this->MerchantSettings->getMerchantId() . '</Mid>'
            . '</Authentication>'
            . $order_info
            . '<PaymentInfo>'
            . $pay_method_xml
            . $card_fields
            . $recurring_xml
            . $installments_xml
            . $tokens
            . $three_ds_secure
            . '</PaymentInfo>'
            . '</' . $transaction_type . '>';

        $message_xml = '<Message xmlns="' . $xmlns_url . '" xmlns:ns2="' . $xmlns_ns2_url . '" version="' . $version . '">'
            . $data_xml_inner
            . '</Message>';

        $digested_xml = $message_xml . $this->MerchantSettings->getSharedSecret();
        $digest = base64_encode(hash('sha256', $digested_xml, true));

        $input_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<VPOS xmlns="' . $xmlns_url . '" xmlns:ns2="' . $xmlns_ns2_url . '">'
            . $message_xml
            . '<Digest>' . $digest . '</Digest>'
            . '</VPOS>';

        switch ($this->PaymentSettings->getAcquirer()) {
            case \Cardlink\Payments\Constants\Acquirer::NEXI:
                $vposXmlGatewayUrl = ($this->PaymentSettings->isProduction())
                    ? 'https://www.alphaecommerce.gr/vpos/xmlpayvpos'
                    : 'https://alphaecommerce-test.cardlink.gr/vpos/xmlpayvpos';
                break;

            case \Cardlink\Payments\Constants\Acquirer::WORLDLINE:
                $vposXmlGatewayUrl = ($this->PaymentSettings->isProduction())
                    ? 'https://vpos.eurocommerce.gr/vpos/xmlpayvpos'
                    : 'https://eurocommerce-test.cardlink.gr/vpos/xmlpayvpos';
                break;

            case \Cardlink\Payments\Constants\Acquirer::CARDLINK:
            default:
                $vposXmlGatewayUrl = ($this->PaymentSettings->isProduction())
                    ? 'https://ecommerce.cardlink.gr/vpos/xmlpayvpos'
                    : 'https://ecommerce-test.cardlink.gr/vpos/xmlpayvpos';
                break;
        }

        $httpClient = new GuzzleHttp\Client([
            'timeout' => 120,
            'connect_timeout' => 300,
            'allow_redirects' => true,
            'debug' => false,
            'synchronous' => true,
            'version' => 1.1 // HTTP 1.1
        ]);

        $httpResponse = $httpClient->post($vposXmlGatewayUrl, [
            'body' => $input_xml,
            'headers' => [
                'Content-Type' => 'application/xml',
                'Content-length' => strlen($input_xml)
            ]
        ]);

        if ($httpResponse->getStatusCode() != 200) {
            return [
                'status' => $httpResponse->getStatusCode(),
                'message' => $httpResponse->getReasonPhrase()
            ];
        }

        $array_data = json_decode(json_encode(simplexml_load_string($httpResponse->getBody()->getContents())), true);

        if ($this->isDebugMode()) {
            error_log("Input XML: " . $input_xml, $this->defaultLogFacility);
            error_log("Response Data: " . json_encode($array_data), $this->defaultLogFacility);
        }

        $isSaleResponse = array_key_exists('SaleResponse', $array_data['Message']);
        $responseData = $isSaleResponse ? $array_data['Message']['SaleResponse'] : $array_data['Message']['AuthorisationResponse'];
        $success = ($responseData['Status'] == 'AUTHORIZED' || $responseData['Status'] == 'CAPTURED');

        if ($success && $extTokenOptions === '100') {
            // Execute callback if provided to allow merchant code to handle the credit card token.
            if (isset($storeCardTokenCallback) && is_callable($storeCardTokenCallback)) {
                $cardToken = new \Cardlink\Payments\Models\CardToken();
                $cardToken->loadPaymentGatewayData($pay_method, $responseData);
                $storeCardTokenCallback($cardToken);
            }
        }

        return [
            'status' => $success ? 200 : 400,
            'message' => $responseData['Status'],
            'transaction' => $responseData,
        ];
    }

    public function getDefaultStyleSheets(): string
    {
        return file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'default-custom-styles.css');
    }

    /**
     * Get the Javascript code used to encode card details.
     * 
     * @return array
     */
    public function getCardEncodingJavaScriptCode(): array
    {
        $timezone = isset($this->timezone) ? $this->timezone : new DateTimeZone("Europe/Athens");

        $data = [
            'version' => 2,
            'mid' => $this->MerchantSettings->getMerchantId(),
            'date' => (new DateTime('NOW', $timezone))->format('YmdHi')
        ];

        // Generate the message digest
        $form_data = implode('', $data) . $this->MerchantSettings->getSharedSecret();
        $data['digest'] = base64_encode(hash('sha256', $form_data, true));

        $httpClient = new GuzzleHttp\Client([
            'timeout' => 120,
            'connect_timeout' => 45,
            'allow_redirects' => true,
            'debug' => false,
            'synchronous' => true,
            'version' => 1.1 // HTTP 1.1
        ]);

        switch ($this->PaymentSettings->getAcquirer()) {
            case \Cardlink\Payments\Constants\Acquirer::NEXI:
                $mpiUrl = ($this->PaymentSettings->isProduction())
                    ? 'https://www.alphaecommerce.gr/vpos/csescript.js'
                    : 'https://alphaecommerce-test.cardlink.gr/vpos/csescript.js';
                break;

            case \Cardlink\Payments\Constants\Acquirer::WORLDLINE:
                $mpiUrl = ($this->PaymentSettings->isProduction())
                    ? 'https://vpos.eurocommerce.gr/vpos/csescript.js'
                    : 'https://eurocommerce-test.cardlink.gr/vpos/csescript.js';
                break;

            case \Cardlink\Payments\Constants\Acquirer::CARDLINK:
            default:
                $mpiUrl = ($this->PaymentSettings->isProduction())
                    ? 'https://ecommerce.cardlink.gr/vpos/csescript.js'
                    : 'https://ecommerce-test.cardlink.gr/vpos/csescript.js';
                break;
        }

        $response = $httpClient->get($mpiUrl . '?' . http_build_query($data));

        $jsBody = $response->getBody()->getContents();
        $jsSuccess = $response->getStatusCode() && !stristr($jsBody, 'CSE Script loading failure');

        return [
            'status' => $jsSuccess ? 200 : 400,
            'message' => $jsSuccess ? 'OK' : $response->getReasonPhrase(),
            'url' => $mpiUrl . '?' . http_build_query($data),
            'body' => $jsBody,
        ];
    }

    /**
     * Get the URL of the CSS file to use for the IRIS and PayPal payment UI.
     * 
     * @return string
     */
    private function getCssUrl(): string
    {
        $isValidCustomCssUrl = filter_var(
            $this->customCssUrl,
            FILTER_VALIDATE_URL | FILTER_FLAG_PATH_REQUIRED
        ) !== false;

        if ($isValidCustomCssUrl) {
            return $this->customCssUrl;
        }

        switch ($this->PaymentSettings->getAcquirer()) {
            case \Cardlink\Payments\Constants\Acquirer::NEXI:
                return ($this->PaymentSettings->isProduction())
                    ? 'https://www.alphaecommerce.gr/vposart/alpha/v2/css/styles_main_SDK.css'
                    : 'https://alphaecommerce-test.cardlink.gr/vposart/alpha/v2/css/styles_main_SDK.css';

            case \Cardlink\Payments\Constants\Acquirer::WORLDLINE:
                return ($this->PaymentSettings->isProduction())
                    ? 'https://vpos.eurocommerce.gr/vposart/euro/v2/css/styles_main_SDK.css'
                    : 'https://eurocommerce-test.cardlink.gr/vposart/euro/v2/css/styles_main_SDK.css';

            case \Cardlink\Payments\Constants\Acquirer::CARDLINK:
            default:
                return ($this->PaymentSettings->isProduction())
                    ? 'https://ecommerce.cardlink.gr/vposart/cardlink/v2/css/styles_main_SDK.css'
                    : 'https://ecommerce-test.cardlink.gr/vposart/cardlink/v2/css/styles_main_SDK.css';
        }
    }

    /**
     * Create and send a payment request for the IRIS method.
     * 
     * @param array $paymentRequestData Payment request data.
     * @return array
     */
    public function createIrisPaymentRequest(array $paymentRequestData): array
    {
        $lang = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'lang', 'el');
        $order_id = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'orderId', '');
        $orderAmount = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'purchAmount', '');
        $currency = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'currency', 'EUR');
        $payerEmail = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'payerEmail', '');
        $billCountry = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'billCountry', 'GR');
        // $billState = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'billState', '');
        $billZip = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'billZip', '');
        $billCity = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'billCity', '');
        $billAddress = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'billAddress', '');

        $order_id = $order_id ? $order_id : strtolower(\Cardlink\Payments\Helpers\Crypto::generateRandomString(7));

        $orderCurrency = (is_numeric($currency)) ? $this->iso4217->getByNumeric($currency) : $this->iso4217->getByAlpha3($currency);
        $currencyAlpha3 = $orderCurrency['alpha3'];

        $orderBillingCountry = (is_numeric($billCountry)) ? $this->iso3166->numeric($billCountry) : $this->iso3166->alpha2($billCountry);
        $billCountryAlpha2 = $orderBillingCountry['alpha2'];

        $form_data_array = [
            'version' => 2,
            'mid' => $this->MerchantSettings->getMerchantId(),
            'lang' => $lang,
            'orderid' => $order_id . 'at' . date('Ymdhis'),
            'orderDesc' => \Cardlink\Payments\Helpers\Tools::generateIrisRFCode($this->MerchantSettings->getDiasCustomerCode(), $order_id, $orderAmount),
            'orderAmount' => substr_replace($orderAmount, '.', strlen($orderAmount) - 2, 0),
            'currency' => $currencyAlpha3,
            'payerEmail' => $payerEmail,
            'billCountry' => $billCountryAlpha2,
            //'billState'   => $billState,
            'billZip' => $billZip,
            'billCity' => $billCity,
            'billAddress' => $billAddress,
            'payMethod' => 'IRIS',
            'cssUrl' => $this->getCssUrl(),
            'confirmUrl' => $this->RouteSettings->getIrisPaymentSuccessUrl(),
            'cancelUrl' => $this->RouteSettings->getIrisPaymentFailedUrl(),
        ];

        $posted_data_string = '';
        foreach ($form_data_array as $k => $v) {
            $posted_data_string .= htmlspecialchars($v);
        }
        $form_data = iconv('utf-8', 'utf-8//IGNORE', $posted_data_string) . $this->MerchantSettings->getSharedSecret();
        $form_data_array['digest'] = base64_encode(hash('sha256', $form_data, true));

        switch ($this->PaymentSettings->getAcquirer()) {
            case \Cardlink\Payments\Constants\Acquirer::NEXI:
                $form_post_url = ($this->PaymentSettings->isProduction())
                    ? 'https://www.alphaecommerce.gr/vpos/shophandlermpi'
                    : 'https://alphaecommerce-test.cardlink.gr/vpos/shophandlermpi';
                break;

            case \Cardlink\Payments\Constants\Acquirer::WORLDLINE:
                $form_post_url = ($this->PaymentSettings->isProduction())
                    ? 'https://vpos.eurocommerce.gr/vpos/shophandlermpi'
                    : 'https://eurocommerce-test.cardlink.gr/vpos/shophandlermpi';
                break;

            case \Cardlink\Payments\Constants\Acquirer::CARDLINK:
            default:
                $form_post_url = ($this->PaymentSettings->isProduction())
                    ? 'https://ecommerce.cardlink.gr/vpos/shophandlermpi'
                    : 'https://ecommerce-test.cardlink.gr/vpos/shophandlermpi';
                break;
        }

        $data = [
            'status' => 200,
            'endpoint' => $form_post_url,
            'form_data' => $form_data_array,
            'form_html' => \Cardlink\Payments\Helpers\Tools::createAutoSubmittingForm($form_post_url, $form_data_array)
        ];

        if ($this->isDebugMode()) {
            error_log('post_iris_payment data', $this->defaultLogFacility);
            error_log(json_encode($form_data_array), $this->defaultLogFacility);
        }

        return $data;
    }

    /**
     * Process the data returned by an IRIS payment transaction.
     * 
     * @param array $transactionData Data returned by the payment gateway regarding the transaction.
     * @return array
     */
    public function processIrisPaymentResponse(array $transactionData): array
    {
        if ($this->isDebugMode()) {
            error_log('post_iris_payment_response', $this->defaultLogFacility);
            error_log(json_encode($transactionData), $this->defaultLogFacility);
        }

        $message_data = '';

        foreach ($transactionData as $key => $value) {
            if ($key != 'digest') {
                $message_data .= $value;
            }
        }

        // Test digest validity
        $digested_data = $message_data . $this->MerchantSettings->getSharedSecret();
        $digest = base64_encode(hash('sha256', $digested_data, true));

        if ($digest == $transactionData['digest']) {
            $is_success = $transactionData['status'] == 'AUTHORIZED' || $transactionData['status'] == 'CAPTURED';

            $data = [
                'status' => $is_success ? 200 : 400,
                'message' => $transactionData['status'],
                'data' => $transactionData
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'Invalid digest',
                'data' => []
            ];
        }

        return $data;
    }

    /**
     * Create and send a payment request for the PayPal method.
     * 
     * @param array $paymentRequestData Payment request data.
     * @return array
     */
    public function createPayPalPaymentRequest(array $paymentRequestData): array
    {
        $lang = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'lang', 'el');
        $order_id = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'orderId', '');
        $orderAmount = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'purchAmount', '');
        $currency = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'currency', 'EUR');
        $payerEmail = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'payerEmail', '');
        $billCountry = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'billCountry', 'GR');
        // $billState = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'billState', '');
        $billZip = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'billZip', '');
        $billCity = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'billCity', '');
        $billAddress = \Cardlink\Payments\Helpers\Tools::extractFromArray($paymentRequestData, 'billAddress', '');

        $order_id = $order_id ? $order_id : strtolower(\Cardlink\Payments\Helpers\Crypto::generateRandomString(7));

        $orderCurrency = (is_numeric($currency)) ? $this->iso4217->getByNumeric($currency) : $this->iso4217->getByAlpha3($currency);
        $currencyAlpha3 = $orderCurrency['alpha3'];

        $orderBillingCountry = (is_numeric($billCountry)) ? $this->iso3166->numeric($billCountry) : $this->iso3166->alpha2($billCountry);
        $billCountryAlpha2 = $orderBillingCountry['alpha2'];

        $form_data_array = [
            'version' => 2,
            'mid' => $this->MerchantSettings->getMerchantId(),
            'lang' => $lang,
            'orderid' => $order_id . 'at' . date('Ymdhis'),
            'orderDesc' => $order_id,
            'orderAmount' => substr_replace($orderAmount, '.', strlen($orderAmount) - 2, 0),
            'currency' => $currencyAlpha3,
            'payerEmail' => $payerEmail,
            'billCountry' => $billCountryAlpha2,
            //'billState'   => $billState,
            'billZip' => $billZip,
            'billCity' => $billCity,
            'billAddress' => $billAddress,
            'payMethod' => 'PayPalREST',
            'cssUrl' => $this->getCssUrl(),
            'confirmUrl' => $this->RouteSettings->getPayPalPaymentSuccessUrl(),
            'cancelUrl' => $this->RouteSettings->getPayPalPaymentFailedUrl(),
        ];

        $posted_data_string = '';
        foreach ($form_data_array as $k => $v) {
            $posted_data_string .= htmlspecialchars($v);
        }
        $form_data = iconv('utf-8', 'utf-8//IGNORE', $posted_data_string) . $this->MerchantSettings->getSharedSecret();
        $form_data_array['digest'] = base64_encode(hash('sha256', $form_data, true));

        switch ($this->PaymentSettings->getAcquirer()) {
            case \Cardlink\Payments\Constants\Acquirer::NEXI:
                $form_post_url = ($this->PaymentSettings->isProduction())
                    ? 'https://www.alphaecommerce.gr/vpos/shophandlermpi'
                    : 'https://alphaecommerce-test.cardlink.gr/vpos/shophandlermpi';
                break;

            case \Cardlink\Payments\Constants\Acquirer::WORLDLINE:
                $form_post_url = ($this->PaymentSettings->isProduction())
                    ? 'https://vpos.eurocommerce.gr/vpos/shophandlermpi'
                    : 'https://eurocommerce-test.cardlink.gr/vpos/shophandlermpi';
                break;

            case \Cardlink\Payments\Constants\Acquirer::CARDLINK:
            default:
                $form_post_url = ($this->PaymentSettings->isProduction())
                    ? 'https://ecommerce.cardlink.gr/vpos/shophandlermpi'
                    : 'https://ecommerce-test.cardlink.gr/vpos/shophandlermpi';
                break;
        }

        $data = [
            'status' => 200,
            'form_data' => $form_data_array,
            'endpoint' => $form_post_url,
            'form_html' => \Cardlink\Payments\Helpers\Tools::createAutoSubmittingForm($form_post_url, $form_data_array)
        ];

        if ($this->isDebugMode()) {
            error_log('post_paypal_payment data', $this->defaultLogFacility);
            error_log(json_encode($form_data_array), $this->defaultLogFacility);
        }

        return $data;
    }

    /**
     * Process the data returned by an PayPal payment transaction.
     * 
     * @param array $transactionData Data returned by the payment gateway regarding the transaction.
     * @return array
     */
    public function processPayPalPaymentResponse(array $transactionData): array
    {
        if ($this->isDebugMode()) {
            error_log('post_paypal_payment_response', $this->defaultLogFacility);
            error_log(json_encode($transactionData), $this->defaultLogFacility);
        }

        $message_data = '';

        foreach ($transactionData as $key => $value) {
            if ($key != 'digest') {
                $message_data .= $value;
            }
        }

        // Test digest validity
        $digested_data = $message_data . $this->MerchantSettings->getSharedSecret();
        $digest = base64_encode(hash('sha256', $digested_data, true));

        if ($digest == $transactionData['digest']) {
            $is_success = $transactionData['status'] == 'AUTHORIZED' || $transactionData['status'] == 'CAPTURED';

            $data = [
                'status' => $is_success ? 200 : 400,
                'message' => $transactionData['status'],
                'data' => $transactionData
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'Invalid digest',
                'data' => []
            ];
        }

        return $data;
    }
}