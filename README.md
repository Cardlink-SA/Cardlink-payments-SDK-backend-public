# Cardlink Payments SDK for PHP

## Introduction

The PHP SDK for Cardlink Payments has been developed as a library meant to be used in a custom-built payment module of a PHP based e-commerce platform (e.g. WooCommerce, PrestaShop, Magento, etc.). The e-commerce platform will need to expose a set of HTTP endpoints that will be used by the mobile application's payment SDK library to handle all configured payment functionalities (Debit/Credit Card, IRIS, PayPal).

### Requirements

- PHP version 7.4.x or PHP 8.x

### Limitations

The PHP SDK library will not handle functionalities performed by the e-commerce platform such as retrieving customer information, creating/editing customers, placing orders, retrieving, or storing card tokens. These operations must be performed by the e-commerce platform's custom payment module.

## Exposed API Endpoints

The merchant's E-Commerce platform will need to implement the following functions at minimum. The corresponding URLs will be configured on the mobile SDK clients.


| Example URL | HTTP Verb | Description |
| -- | -- | -- |
| `/api/get-js` | GET | Get the JS code that will be used to encode card details for safe transport. |
| `/api/get-stylesheets` | GET | Get the custom CSS code that will be used to style the payment gateway. |
| `/api/user-cards` | GET | Get a list of the customer's stored card tokens. |
| `/api/user-cards/delete` | DELETE | Delete a stored card token. |
| `/api/settings` | GET | Get the configuration settings. |
| `/api/payment` | POST | The URL that the mobile client will send order and customer information to initiate a credit/debug card payment request process. |
| `/api/payment/success` | POST | The URL that the payment gateway will return to after a successful payment transaction (credit card) carrying transaction related information. |
| `/api/payment/fail` | POST | The URL that the payment gateway will return to after a canceled/failed payment transaction (credit card) carrying transaction related information. |
| `/api/payment/iris` | POST | The URL that the mobile client will send order and customer information to initiate an IRIS payment request process. |
| `/api/payment/iris/response` | POST | The URL that the payment gateway will return to after a successful/failed/canceled payment transaction carrying transaction related information. |
| `/api/payment/paypal` | POST | The URL that the mobile client will send order and customer information to initiate a PayPal payment request process. |
| `/api/payment/paypal/response` | POST | The URL that the payment gateway will return to after a successful/failed/canceled payment transaction carrying transaction related information. |


## Client Configuration

### Merchant Settings

| Setting | Required | Description |
| -- | -- | -- |
| Merchant ID | Y | The ID of the merchant. |
| Shared Secret | Y | The Shared Secret Key used to sign merchant requests. |
| Merchant Private Key | Y | The Merchant Private Key used to sign merchant requests. |
| Processor Certificate | Y | The certificate used to validate responses signed by the processor. |
| DIAS Customer Code | N | The customer code of the DIAS transaction network (used for IRIS transactions). |


#### **setMerchantId(string $merchant_id)**

Sets the merchant ID.

| Parameter | Type | Description |
| -- | -- | -- |
| `$merchant_id` | string | The ID of the merchant. |

Returns: The `\Cardlink\Payments\Settings\MerchantSettings` object.


#### **getMerchantId(): string**

Gets the currently configured merchant ID.

Returns: The string value of the merchant ID configuration setting.


#### **setSharedSecret(string $shared_secret)**

Sets the value of the shared secret key.

| Parameter | Type | Description |
| -- | -- | -- |
| `$shared_secret` | string | The shared secret key between the merchant and the payment gateway. |

Returns: The `\Cardlink\Payments\Settings\MerchantSettings` object.


#### **getSharedSecret(): string**

Gets the currently configured shared secret key.

Returns: The string value of the shared secret key configuration setting.


#### **setMerchantPrivateKey(string $merchant_private_key)**

Sets the value of the merchant's RSA private key in PKCS8 format.

| Parameter | Type | Description |
| -- | -- | -- |
| `$merchant_private_key` | string | The merchant's RSA private key used to sign messages sent to the processor. |

Returns: The `\Cardlink\Payments\Settings\MerchantSettings` object.


#### **getMerchantPrivateKey(): string**

Gets the currently configured value of the merchant's RSA private key.

Returns: The string value of the merchant's RSA private key.


#### **setProcessorCertificate(string $processor_certificate)**

Sets the value of the processor's RSA public key in X509 format.

| Parameter | Type | Description |
| -- | -- | -- |
| `$processor_certificate` | string | The processor's RSA public key used to verify signed messages sent to the merchant. |

Returns: The `\Cardlink\Payments\Settings\MerchantSettings` object.


#### **getProcessorCertificate(): string**

Gets the currently configured value of the processor's RSA public key in X509 format.

Returns: The string value of the processor's RSA public key configuration setting.


#### **setDiasCustomerCode(string $diasCode)**

Sets the value of the merchant's customer code on the DIAS network.

| Parameter | Type | Description |
| -- | -- | -- |
| `$diasCode` | string | The merchant's customer code on the DIAS network. |

Returns: The `\Cardlink\Payments\Settings\MerchantSettings` object.


#### **getDiasCustomerCode(): string**
Gets the merchant's currently configured customer code on the DIAS network.

Returns: The string value of the merchant's customer code on the DIAS network configuration setting.


### Payment Settings

| Setting | Required | Default | Description |
| -- | -- | -- | -- |
| IsProduction | Y | `FALSE` | Identifies the transaction as test or production. |
| Currency | Y | '`EUR`' | The currency code of all transactions. |
| MaxInstallments | Y | `0` | The maximum number of allowed installments. Numeric value in range 0-60. |
| InstallmentsVariations | Y | `[]` | Array of installment variation objects describing ranges of order amounts and the maximum allowed number of installments. |
| AllowsTokenization | Y | `FALSE` | Defines whether the merchant's platform allows card tokenization. |
| Acquirer | Y | '`cardlink`' | The name of the acquirer. Valid values are: `cardlink`, `nexi`, `worldline`. |
| AcceptedCardTypes | Y | `[]` | Array of the names of accepted debit/credit card brands. Valid values are `visa`, `mastercard`, `maestro`, `diners`, `discover`, `amex`. The merchant must select the cards that the bank supports and are agreed upon. |
| AcceptedPaymentMethods | Y | `[]` | Array of names of the accepted payment methods. Valid values are card, iris, paypal. |


#### **setIsProduction(bool $is_production)**

Sets the value of the merchant's customer code on the DIAS network.

| Parameter | Type | Description |
| -- | -- | -- |
| `$is_production` | bool | Identifies the transaction environment as production (`TRUE`) or testing (`FALSE`). |

Returns: The ``\Cardlink\Payments\Settings\PaymentSettings`` object.


#### **isProduction(): bool**

Gets the currently configured transaction environment.

Returns: `TRUE` for live transactions environment. `FALSE` for the test transactions environment.


#### **setCurrency(string $currency)**

Sets the default currency for all transactions.

| Parameter | Type | Description |
| -- | -- | -- |
| `$currency` | string | A string containing the currency code (e.g., "`EUR`"). |

Returns: The `\Cardlink\Payments\Settings\PaymentSettings` object.


#### **getCurrency(): string**

Gets the currently configured currency code.

Returns: The string value of the currently configured currency code.


#### **setAllowsTokenization(bool $allows_tokenization)**

Enables/disables the tokenization of customer cards.

| Parameter | Type | Description |
| -- | -- | -- |
| `$allows_tokenization` | bool | Allows the customer to select whether the payment module will tokenize the card details (`TRUE`) or not (`FALSE`). |

Returns: The `\Cardlink\Payments\Settings\PaymentSettings` object.


#### **allowsTokenization(): bool**

Gets the currently configured setting for allowing customers to choose whether the payment module should tokenize their card details. The customer must explicitly opt-in to have their card details tokenized.

Returns: True: The customer is given the option to choose whether the payment gateway will tokenize their card details (tokenization is allowed). False: the customer will not be given the option to tokenize their card details (tokenization is disabled).


#### **setInstallmentsVariations($installments_variations)**

Sets the installments variations setting which controls the maximum number of installments that the customer can select according to the order amount. E.g., max 3 installments for orders up to 100€, max 6 installments for orders up to 200€, etc.

| Parameter | Type | Description |
| -- | -- | -- |
| `$installments_variations` | Array of `\Cardlink\Payments\Models\InstallmentsVariation` objects | Identifies the transaction environment as production (`TRUE`) or testing (`FALSE`). |

Returns: The `\Cardlink\Payments\Settings\PaymentSettings` object.


#### **getInstallmentsVariations(): array**

Gets the currently configured installments variations.

Returns: Array of `\Cardlink\Payments\Models\InstallmentsVariation` objects.


#### **getSanitizedInstallmentsVariations(): array**

Gets the sanitized version of the currently configured installments variations.

Returns: Array of `\Cardlink\Payments\Models\InstallmentsVariation` objects. The number of installments is limited to the range of 0-60 installments. A value of `1` is automatically converted to `0`. Variations of configured order amount of `0` or negative value are automatically filtered out.


#### **addInstallmentsVariation(\Cardlink\Payments\Models\InstallmentsVariation $installments_variation)**

Adds an installments variation object to the respective installments variations array.

| Parameter | Type | Description |
| -- | -- | -- |
| `$installments_variation` | `\Cardlink\Payments\Models\InstallmentsVariation` | Identifies the transaction environment as production (`TRUE`) or testing (`FALSE`). |

Returns: True if the input installments variation object was successfully added to the array.


#### **setAcquirer(string $acquirer)**

Sets the name of the configured acquirer service.

| Parameter | Type | Description |
| -- | -- | -- |
| `$acquirer` | string | The key name of the acquirer that will process the payment transaction. Valid values are '`cardlink`' (default), '`nexi`' or '`worldline`'. |

Returns: The `\Cardlink\Payments\Settings\PaymentSettings` object.


#### **getAcquirer(): string**

Gets the currently configured acquirer service.

Returns: The key name of the currently configured acquirer. Valid values are '`cardlink`' (default), '`nexi`' or '`worldline`'.


#### **setAcceptedPaymentMethods(array $accepted_payment_methods)**

Sets the list of accepted payment methods.

| Parameter | Type | Description |
| -- | -- | -- |
| `$accepted_payment_methods` | Array of strings | Array containing the key names of the accepted payment methods. Valid values are '`card`', '`iris`' or '`paypal`'. |

Returns: The `\Cardlink\Payments\Settings\PaymentSettings` object.


#### **getAcceptedPaymentMethods(): array**

Gets the currently configured list of accepted payment methods.

Returns: Array containing the key names of the accepted payment methods. Valid values are '`card`', '`iris`' or '`paypal`'.


#### **setAcceptedCardTypes(array $accepted_card_types)**

Sets the list of accepted card types (brands).

| Parameter | Type | Description |
| -- | -- | -- |
| `$accepted_card_types` | Array of strings | Array containing the key names of the accepted card types. Valid values are '`visa`', '`mastercard`', '`diners`', '`discover`' and '`amex`'. |

Returns: The `\Cardlink\Payments\Settings\PaymentSettings` object.


#### **getAcceptedCardTypes(): array**

Gets the currently configured list of accepted card types (brands).

Returns: Array containing the key names of the accepted card types. Valid values are '`visa`', '`mastercard`', '`diners`', '`discover`' and '`amex`'.


#### **setMaxInstallments(int $max_installments)**

Sets the maximum allowed number of installments regardless of the order amount. The option only applies when a credit card is being used for the transaction.

| Parameter | Type | Description |
| -- | -- | -- |
| `$max_installments` | int | The maximum number of installments a customer can select for his payment transaction. |

Returns: The `\Cardlink\Payments\Settings\PaymentSettings` object.


#### **getMaxInstallments(): int**

Gets the currently configured maximum allowed number of installments regardless of the order amount.

Returns: The currently configured maximum allowed number of installments. The default value is `0`.


### Route Settings

| Setting | Description | Getter/Setter Methods |
| -- | -- | -- |
| Payment Request URL | The URL that the mobile clients will send customer and order information to initiate a card payment transaction. | `getPaymentRequestUrl(): string` <br /> `setPaymentRequestUrl(string $paymentRequestUrl): \Cardlink\Payments\Settings\RouteSettings` |
| Payment Success URL | The return URL after a successful payment transaction (credit/debit card). | `getPaymentSuccessUrl(): string` <br /> `setPaymentSuccessUrl(string $paymentSuccessUrl): \Cardlink\Payments\Settings\RouteSettings` |
| Payment Failed URL | The return URL after a canceled/failed payment transaction (credit/debit card). | `getPaymentFailedUrl(): string` <br /> `setPaymentFailedUrl(string $paymentFailedUrl): \Cardlink\Payments\Settings\RouteSettings` |
| IRIS Payment Request URL | The URL that the mobile clients will send customer and order information to initiate an IRIS payment transaction. | `getIrisPaymentRequestUrl(): string` <br /> `setIrisPaymentRequestUrl(string $irisPaymentRequestUrl): \Cardlink\Payments\Settings\RouteSettings` |
| IRIS Payment Success URL | The return URL after a successful IRIS payment transaction. | `getIrisPaymentSuccessUrl(): string` <br /> `setIrisPaymentSuccessUrl(string $irisPaymentSuccessUrl): \Cardlink\Payments\Settings\RouteSettings` |
| IRIS Payment Failed URL | The return URL after a canceled/failed IRIS payment transaction. | `getIrisPaymentFailedUrl(): string` <br /> `setIrisPaymentFailedUrl(string $irisPaymentFailedUrl): \Cardlink\Payments\Settings\RouteSettings` |
| PayPal Payment Request URL | The URL that the mobile clients will send customer and order information to initiate a PayPal payment transaction. | `getPayPalPaymentRequestUrl(): string` <br /> `setPayPalPaymentRequestUrl(string $paypalPaymentRequestUrl): \Cardlink\Payments\Settings\RouteSettings` |
| PayPal Payment Success URL | The return URL after a successful PayPal payment transaction. | `getPayPalPaymentSuccessUrl(): string` <br /> `setPayPalPaymentSuccessUrl(string $paypalPaymentSuccessUrl): \Cardlink\Payments\Settings\RouteSettings` |
| PayPal Payment Failed URL | The return URL after a canceled/failed PayPal payment transaction. | `getPayPalPaymentFailedUrl(): string` <br /> `setPayPalPaymentFailedUrl(string $paypalPaymentFailedUrl): \Cardlink\Payments\Settings\RouteSettings` |
| Custom UI CSS URL | The URL that the custom UI stylesheets (CSS) code will be available for the payment gateway. If not set, or empty, no custom stylesheets will be applied. | `getUiStyleSheetsUrl(): string` <br /> `setUiStyleSheetsUrl(string $uiStyleSheetsUrl): \Cardlink\Payments\Settings\RouteSettings` |



## Configuration Example

```php
$instance = new Cardlink_Payment_Gateway_Woocommerce();

$this->client = new \Cardlink\Payments\Client();
$this->client
    ->setTimezone("Europe/Athens")
    ->setDebugMode(false);

$this->client->MerchantSettings
    ->setMerchantId($instance->get_merchant_id())
    ->setSharedSecret($instance->get_shared_secret())
    ->setDiasCustomerCode($instance->get_dias_customer_code())
    ->setMerchantPrivateKey($instance->get_merchant_private_key());

if ($instance->is_production()) {
	$this->client->MerchantSettings->setProcessorCertificate(file_get_contents(dirname(__FILE__) . '/cardlink-payments-sdk-php/data/processor-production-certificate.cer'));
} else {
	$this->client->MerchantSettings->setProcessorCertificate(file_get_contents(dirname(__FILE__) . '/cardlink-payments-sdk-php/data/processor-test-certificate.cer'));
}

$this->client->RouteSettings
	->setUiStyleSheetsUrl('https://' . $_SERVER['HTTP_HOST'] . '/wp-json/' . $this->namespace . '/get-stylesheets')
    ->setPaymentRequestUrl('https://' . $_SERVER['HTTP_HOST'] . '/wp-json/' . $this->namespace . '/payment/')
    ->setPaymentFailedUrl('https://' . $_SERVER['HTTP_HOST'] . '/wp-json/' . $this->namespace . '/payment/fail/')
    ->setPaymentSuccessUrl('https://' . $_SERVER['HTTP_HOST'] . '/wp-json/' . $this->namespace . '/payment/success/')
    ->setIrisPaymentRequestUrl('https://' . $_SERVER['HTTP_HOST'] . '/wp-json/' . $this->namespace . '/payment/iris/')
    ->setIrisPaymentFailedUrl('https://' . $_SERVER['HTTP_HOST'] . '/wp-json/' . $this->namespace . '/payment/iris/response/')
    ->setIrisPaymentSuccessUrl('https://' . $_SERVER['HTTP_HOST'] . '/wp-json/' . $this->namespace . '/payment/iris/response/')
    ->setPayPalPaymentRequestUrl('https://' . $_SERVER['HTTP_HOST'] . '/wp-json/' . $this->namespace . '/payment/paypal/')
    ->setPayPalPaymentFailedUrl('https://' . $_SERVER['HTTP_HOST'] . '/wp-json/' . $this->namespace . '/payment/paypal/response/')
    ->setPayPalPaymentSuccessUrl('https://' . $_SERVER['HTTP_HOST'] . '/wp-json/' . $this->namespace . '/payment/paypal/response/');

$this->client->PaymentSettings
	->setIsProduction($instance->is_production())
	->setCaptureMode($instance->is_capture_mode())
    ->setAcquirer($instance->get_acquirer())
    ->setCurrency(get_woocommerce_currency())
    ->setAcceptedCardTypes($instance->get_accepted_card_types())
    ->setAcceptedPaymentMethods($instance->get_accepted_payment_methods())
    ->setAllowsTokenization(sanitize_text_field($instance->get_option('tokenization')) == 'yes' ? true : false)
    ->setMaxInstallments(absint($instance->get_option('installments')));
```

## Client API Methods


#### **isDebugMode()**

Gets the current state of the debug mode.

Returns: A boolean value indicating whether the client will execute in debug mode thus logging information in the web server's logging facility.


#### **setDebugMode($debugMode)**

Sets the active state of the debug mode.

| Parameter | Type | Description |
| -- | -- | -- |
| `$debugMode` | bool | The new state of the debug mode. |

Returns: The `\Cardlink\Payments\Client` object.


#### **setTimezone($timezone)**

Sets the timezone of the e-commerce website.

| Parameter | Type | Description |
| -- | -- | -- |
| `$timezone` | string | The timezone of the e-commerce website. Default is "`Europe/Athens`". |

Returns: The `\Cardlink\Payments\Client` object.


#### **setCustomCssUrl($customCssUrl)**

Set the URL of the custom CSS file to be used for IRIS and PayPal transactions. If null or an invalid URL is set, the default CSS files for the SDK will be used.

| Parameter | Type | Description |
| -- | -- | -- |
| `$customCssUrl` | string | The URL of the custom CSS file. |

Returns: The `\Cardlink\Payments\Client` object.


#### **getCardEncodingJavaScriptCode(): array**

Gets the credit/debit card encoding JavaScript code that will be used to encrypt the card details before sending them to the payment module to initiate a payment transaction.

Returns: An associative array containing the following data.

| Key | Type | Description |
| -- | -- | -- |
| `status` | int | The HTTP status code returned by the payment gateway for the JavaScript code request. |
| `message` | string | The HTTP status message returned by the payment gateway for the JavaScript code request. |
| `body` | string | The JavaScript code returned by the payment gateway with merchant-specific encryption algorithm and keys to encode the customer's card details. |

Example of using the `getCardEncodingJavaScriptCode()` method is found below.

```php
public function get_js_content()
{
	$response = $this->client->getCardEncodingJavaScriptCode();
	return new WP_REST_Response($response, $response['status']);
}
```


#### **getPaymentSettingsResponse(): array**

Returns an associative array with the payment settings to be used by the mobile client.

| Key | Type| Description |
| -- | -- | -- | 
| `status` | int | Hardcoded value of `200`. |
| `message` | string | Hardcoded value of '`OK`'. |
| `settings` | array | Associative array containing the settings described below. |

The `$response['settings']` sub-array has the following content.

| Key | Type| Description |
| -- | -- | -- | 
| `currency` | string | The configured currency for payment transactions. Output of the `getCurrency()` method. |
| `accepted_card_types` | array | List of accepted card types (brands). Valid values are '`visa`', '`mastercard`', '`diners`', '`discover`' and '`amex`'. Output of the `getAcceptedCardTypes()` method. |
| `accepted_payment_methods` | array | List of accepted payment methods. Valid values are '`card`', '`iris`' or '`paypal`'. Output of the `getAcceptedPaymentMethods()` method.  |
| `installments` | bool | `TRUE` if installments are allowed. `FALSE` if installments selection will not be available. Output of the `getMaxInstallments()` method.  |
| `max_installments` | int | Maximum number of installments allowed. Output of the `getMaxInstallments()` method. |
| `installments_variations` | array | Output of the `getSanitizedInstallmentsVariations()` method. |
| `tokenization` | bool | `TRUE` if card tokenization option is allowed. `FALSE` card tokenization option is not available. Output of the `allowsTokenization()` method. |
| `acquirer` | string | The name of the configured payment acquirer. Output of the `getAcquirer()` method. |
| `routes` | array | Associative array containing routes to the configured for interactions with the payment gateway.<br /> `card_payment_request`: The URL to initiate a card payment request. <br /> `card_payment_success`: The URL that the payment gateway will redirect to after a successful card payment. <br /> `card_payment_failed`: The URL that the payment gateway will redirect to after a canceled/failed card payment. <br /> `iris_payment_request`: The URL to initiate a IRIS payment request. <br /> `iris_payment_success`: The URL that the payment gateway will redirect to after a successful IRIS payment. <br /> `iris_payment_failed`: The URL that the payment gateway will redirect to after a canceled/failed IRIS payment. <br /> `paypal_payment_request`: The URL to initiate a IRIS payment request. <br /> `paypal_payment_success`: The URL that the payment gateway will redirect to after a successful IRIS payment. <br /> `paypal_payment_failed`: The URL that the payment gateway will redirect to after a canceled/failed IRIS payment. <br />  |

Example of using the `getPaymentSettingsResponse()` method is found below.

```php
public function get_plugin_settings()
{
	$response = $this->client->getPaymentSettingsResponse();
	return new WP_REST_Response($response, $response['status']);
}
```

#### **sendAuthenticationRequest($clientRequestData): array**

Sends an authentication request to the authentication server to verify ownership of the card for the payment transaction.

The `$clientRequestData` parameter is an associative array containing the following information.

| Key | Required | Description |
| -- | -- | -- |
| `order_id` | N | The ID of the order if available. If not present, a random order ID will be generated. |
| `cardType` | Y/N | The type of the card. Avoid using this for security purposes. |
| `pan` | Y/N | The card's Personal Account Number. Avoid using this for security purposes. |
| `expiry` | Y/N | The card's expiration date in mm/yy format. Avoid using this for security purposes. |
| `cvv` | Y/N | The card's CVV/CVC number. Avoid using this for security purposes. |
| `cardEncData` | Y/N | This field holds the encrypted card details as generated by the JavaScript code obtained by the `getCardEncodingJavaScriptCode()` method. Prefer using this over cardType, pan, expiry and cvv fields. |
| `purchAmount` | Y | Purchase amount presented as integer number, i.e., `314` for an amount of `3.14€`. |
| `description` | Y | A short description for the order. Maximum length of 128 characters. |
| `currency` | Y | The currency code or currency ID. For EURO, use either "`EUR`" or "`978`". Internal functions will automatically map the value to the required format. |
| `TDS2CardholderEmail` | Y | The card holder's email address. |
| `TDS2CardholderName` | Y | The card holder's name. |
| `TDS2BillAddrCity` | Y | The card holder's city. |
| `TDS2BillAddrLine1` | Y | The card holder's address. |
| `TDS2BillAddrCountry` | Y | The card holder's country. Use either the country code or the country ID. For Greece, this can be either "`GR`" or "`300`". Internal functions will automatically map the value to the required format. |
| `TDS2BillAddrPostCode` | Y | The card holder's postal code. |
| `recurFreq` | N | Optional. In case recurring payments are supported by the processing system then this parameter can be used to indicate frequency of recurring payments, defines minimum number of days between any two subsequent payments. The number of days equal to 28 is a special value indicating that transactions are to be initiated on a monthly basis. Applicable for card payments only. Max 30. |
| `recurEnd` | N | Recurring end date for PAReq/AReq format YYYYMMDD, if recurFreq is present then recurEnd is required. |
| `installments` | N | User selected number of installments. Integer value >1 and <=999. Installment and recurring parameters cannot be present at the same time. |
| `extTokenOptions` | N | **Value = 100** Displaying payment page with prefilled card data and 3D authentication required, extToken field should be sent for tokenized payments).<br /> **Value = 110** Auto payment without displaying payment page and 3D authentication required (extToken field should be sent for tokenized payments). |
| `extToken` | N | Previously stored card token value. |
| `deviceCategory` | N | Integer length 1, Indicates the type of cardholder device. Supported values are: <br />0 = www<br />1 = legacy mobile (deprecated, means wml interfaces)<br />4 = dtv (deprecated)<br />5 = mobile SDK (for ThreeDSecure 2.x only)<br />6 = for ThreeDS Requestor initiated (3RI) flow (for ThreeDSecure 2.x only)<br /> The default value is 0. |

Returns: An associative array containing the following data.

| Key | Type | Description |
| -- | -- | -- |
| `status` | int | The HTTP status code returned by the payment gateway. |
| `response` | array | The response object of the payment gateway. |
| `merged_data` | array | The actual data sent to the payment gateway along with the calculated signature. |
| `merchant_data` | string | Base64-encoded JSON string of the associative array described below.  |
| `merchant_data_key` | string | Base64-encoded GUID. |

The `merchant_data` string encodes the following associative array.

| Key | Description |
| -- | -- |
| `order_id` | The ID of the order, if available. Copy of the input value in `$clientRequestData`.  |
| `cardType` | The type of the card. Copy of the input value in `$clientRequestData` if present. |
| `pan` | The card's Personal Account Number. Copy of the input value in `$clientRequestData` if present. |
| `expiry` | The card's expiration date in `mm/yy` format. Copy of the input value in `$clientRequestData` if present. |
| `cvv` | The card's CVV/CVC number. Copy of the input value in `$clientRequestData` if present. |
| `cardEncData` | Copy of the input value in `$clientRequestData` if present. |
| `purchAmount` | Copy of the input value in `$clientRequestData`. |
| `description` | Copy of the input value in `$clientRequestData`. |
| `currency` | The currency code in ISO4217 alpha3. |
| `cardholderName` | The card holder's email address. Copy of the input value `TDS2CardholderName` in `$clientRequestData`. |
| `cardholderEmail` | The card holder's name. Copy of the input value `TDS2CardholderEmail` in `$clientRequestData`. |
| `billCity` | The card holder's city. Copy of the input value `TDS2BillAddrCity` in `$clientRequestData`. |
| `billLine1` | The card holder's address. Copy of the input value `TDS2BillAddrLine1` in `$clientRequestData`. |
| `billCountryNum` | The card holder's country. For Greece, this is "`300`". |
| `billCountryAlpha2` | The card holder's country. For Greece, this is "`GR`". |
| `billPostCode` | The card holder's postal code. Copy of the input value `TDS2BillAddrPostCode` in `$clientRequestData`. |
| `recurFreq` | Copy of the input value in `$clientRequestData` if present. |
| `recurEnd` |  Copy of the input value in `$clientRequestData` if present. |
| `installments` |  Copy of the input value in `$clientRequestData` if present. |
| `extToken` |  Copy of the input value in `$clientRequestData` if present. |

The returned `merchant_data_key` and `merchant_data` must be stored in a secure transient storage, e.g., user session as a key-value pair.
The `merchant_data_key` is returned to the e-commerce platform from the payment gateway after a successful payment transaction and is used to identify the order and validate the transaction.

Example of using the `sendAuthenticationRequest()` method in the API endpoint handler.

```php
public function post_payment($request)
{
	$data = $this->client->sendAuthenticationRequest($request->get_params());
	set_transient($data['merchant_data_key'], $data['merchant_data'], 3600);
	return new WP_REST_Response($data['response'], $data['status'], [
		'content-Type' => 'application/json'
	]);
}
```

#### **processAuthenticationServerResponse(array $authenticationServerResponseData, ?string $encodedMerchantData, callable $storeCardTokenCallback = null): array**

Processes the response of the authentication server along with merchant data stored in transient storage after calling `sendAuthenticationRequest()` in previous step.

| Parameter | Type | Description |
| -- | -- | -- |
| `$authenticationServerResponseData` | array | The response of the authentication server. |
| `$encodedMerchantData` | string | The Base64 encoded JSON string of the transaction's transient merchant data. |
| `$storeCardTokenCallback` | callable | Optional. A callable function executed when the customer has requested that the card's details be tokenized. |

The `$storeCardTokenCallback` function receives a `\Cardlink\Payments\Models\CardToken` object as the sole input parameter.

| Parameter | Type | Description |
| -- | -- | -- |
| `$cardToken` | `\Cardlink\Payments\Models\CardToken` | The tokenized card details object. |

The HTTP endpoint handling the payment gateway response must retrieve the transient merchant data using the key returned inside the `$_POST['MD']`. The transient data entry should be deleted after successful processing of the payment gateway response.

```php
public function payment_response($request)
{
	$merchantDataKey = $request->get_param('MD');
	$encodedMerchantData = get_transient($merchantDataKey);
	if (isset($encodedMerchantData)) {
		try {
			$response = $this->client->processAuthenticationServerResponse(
				$request->get_params(),
				$encodedMerchantData,
				function (\Cardlink\Payments\Models\CardToken $cardToken) {
					$this->save_card($cardToken);
				}
			);
			delete_transient($merchantDataKey);
			return new WP_REST_Response($response, $response['status'], [
				'content-Type' => 'application/json'
			]);
		} catch (\Exception $ex) {
			return new WP_REST_Response(
				[
					'error' => $ex
				],
				502,
				[
					'content-Type' => 'application/json'
				]
			);
		}
	} else {
		return new WP_REST_Response(
			[
				'error' => 'Data missing'
			],
			400,
			[
				'content-Type' => 'application/json'
			]
		);
	}
}
```

In a WooCommerce installation the `save_card()` method would look something like this.

```php
private function save_card(Cardlink\Payments\Models\CardToken $cardToken)
{
	$current_user_id = 1; // Use actual identified user ID
	$card_exist = false;
	$tokens = WC_Payment_Tokens::get_customer_tokens($current_user_id, $this->id);
	$token_class = new WC_Payment_Token_Data_Store;
	foreach ($tokens as $key => $tok) {
        $token_meta = $token_class->get_metadata($key);
		if (
            $token_meta['card_type'][0] == $cardToken->cardType &&
			$token_meta['last4'][0] == $cardToken->last4Digits &&
			$token_meta['expiry_year'][0] == $cardToken->expiryYear &&
			$token_meta['expiry_month'][0] == $cardToken->expiryMonth
		) {
            $card_exist = true;
		}
	}
	if (!$card_exist) {
		// Build the token
		$token = new WC_Payment_Token_CC();
		$token->set_token($cardToken->token); // Token comes from payment processor
		$token->set_gateway_id($this->id);
		$token->set_last4($cardToken->last4Digits);
		$token->set_expiry_year($cardToken->expiryYear);
		$token->set_expiry_month($cardToken->expiryMonth);
		$token->set_card_type($cardToken->cardType);
		$token->set_user_id($current_user_id);
		// Save the new token to the database
		$token->save();
		// Set this token as the users new default token
		WC_Payment_Tokens::set_users_default($current_user_id, $token->get_id());
	}
}
```

#### **createIrisPaymentRequest(array $paymentRequestData): array**

Creates a payment request for the IRIS payment method.

| Key | Required | Description |
| -- | -- | -- |
| `order_id` | N | The ID of the order if available. If not present, a random order ID will be generated. |
| `lang` | N | The language of the payment gateway's UI. Default is '`el`' for Greek. |
| `purchAmount` | Y | Purchase amount presented as integer number, i.e., 314 for an amount of 3.14€. |
| `currency` | Y | The currency code or currency ID. For EURO, use either "EUR" or "978". Internal functions will automatically map the value to the required format. The default value is '`EUR`'. |
| `payerEmail` | Y | The card holder's email address. |
| `billCountry` | Y | The card holder's country. Use either the country code or the country ID. For Greece, this can be either "`GR`" or "`300`". Internal functions will automatically map the value to the required format. |
| `billZip` | Y | The card holder's postal code. |
| `billCity` | Y | The card holder's city. |
| `billAddress` | Y | The card holder's address. |

Returns: An associative array containing the following data.

| Key | Type | Description |
| -- | -- | -- |
| `status` | int | Constant value 200. |
| `endpoint` | string | The payment gateway endpoint where the payment request form data must be POSTed to. |
| `form_data` | array | The payment request data. |
| `form_html` | string | HTML code for an auto-submitting form containing the `form_data` that will be POSTed at the `endpoint`.  |

Either the contents of `form_html` can be directly output to the HTML page returned to the mobile client or a custom form generated by the payment module using `form_data` and `endpoint`.

An example of how the method should be used follows.

```php
public function post_iris_payment($request)
{
	$response = $this->client->createIrisPaymentRequest($request->get_params());
	
	header('Content-Type: text/html');
	echo '<!DOCTYPE html>';	
	?>
	<html>
	<head>
		<meta charset="utf-8">
		<title>Redirecting you to IRIS payment gateway...</title>
	</head>
	<body>
		Redirecting you to IRIS payment gateway...
		<?php echo $response['form_html']; ?>
	</body>
	</html>
	<?php	
	exit();
}
```

#### **processIrisPaymentResponse(array $transactionData): array**

Processes the POSTed data returned by the payment gateway. Cryptographic verification that the data originated from the payment gateway using signatures is internally performed.

| Parameter | Type | Description |
| -- | -- | -- |
| `$transactionData` | array | The POSTed payment gateway response data. Direct use of the `$_POST` array is possible. |

Returns: An associative array containing the following data.

| Key | Type | Description |
| -- | -- | -- |
| `status` | int | Process status code. `200` for successful AUTHORIZE/CAPTURED transaction. `400` for any type of error. |
| `message` | string | A status message. Can be either `AUTHORIZED`, `CAPTURED`, `Invalid digest` or any other payment gateway message. |
| `data` | array | A copy of the `$transactionData` array or an empty array if an invalid digest is encountered. |

An example of how the method should be used follows.

```php
public function post_iris_payment_response($request)
{
	$response = $this->client->processIrisPaymentResponse($request->get_params());

    /* Handle AUTHORIZED/CAPTURED statuses */

	return new WP_REST_Response($response, $response['status'], [
		'content-Type' => 'application/json'
	]);
}
```

Using the data inside the `$response['message']` the e-commerce platform can mark the status of the order as paid.

#### **createPayPalPaymentRequest(array $paymentRequestData): array**

Same as the `createIrisPaymentRequest()` method but for requesting a payment using the PayPal method.

#### **processPayPalPaymentResponse(array $transactionData): array**

Same as the `processIrisPaymentResponse()` method but for payments using the PayPal method.
