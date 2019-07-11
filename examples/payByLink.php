<?php
/**
 * Include composer class autoloader
 */
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use PayU\Alu\Billing;
use PayU\Alu\Client;
use PayU\Alu\Delivery;
use PayU\Alu\MerchantConfig;
use PayU\Alu\Order;
use PayU\Alu\Product;
use PayU\Alu\Request;
use PayU\Alu\User;
use PayU\Alu\Exceptions\ConnectionException;
use PayU\Alu\Exceptions\ClientException;

/**
 * Create configuration with params:
 *
 * Merchant Code - Your PayU Merchant Code
 * Secret Key - Your PayU Secret Key
 * Platform - RO | RU | UA | TR | HU
 */
$cfg = new MerchantConfig('MERCHANT_CODE', 'SECRET_KEY', 'TR');

/**
 * Create user with params:
 *
 * User IP - User's IP address
 * User Time  - Time of user computer - optional
 *
 */
$user = new User('127.0.0.1');

/**
 * Create new order
 */
$order = new Order();

/**
 * Setup the order params
 *
 * Full params available in the documentation
 */
$order->withBackRef('http://path/to/your/returnUrlScript')
    ->withOrderRef('MerchantOrderRef')
    ->withCurrency('TRY')
    ->withOrderDate(gmdate('Y-m-d H:i:s'))
    ->withOrderTimeout(1000)
    ->withPayMethod('BKM');

/**
 * Create new product
 */
$product = new Product();

/**
 * Setup the product params
 *
 * Full params available in the documentation
 */
$product->withCode('PCODE01')
    ->withName('PNAME01')
    ->withPrice(100.0)
    ->withVAT(24.0)
    ->withQuantity(1);

/**
 * Add the product to the order
 */
$order->addProduct($product);

/**
 * Create new billing address
 */
$billing = new Billing();

/**
 * Setup the billing address params
 *
 * Full params available in the documentation
 */
$billing->withAddressLine1('Address1')
    ->withAddressLine2('Address2')
    ->withCity('City')
    ->withCountryCode('RO')
    ->withEmail('john.doe@mail.com')
    ->withFirstName('FirstName')
    ->withLastName('LastName')
    ->withPhoneNumber('40123456789')
    ->withIdentityCardNumber('111222');

/**
 * Create new delivery address
 *
 * If you want to have the same delivery as billing, skip these two steps
 * and pass the Billing $billing object to the request twice
 */
$delivery = new Delivery();

/**
 * Setup the delivery address params
 *
 * Full params available in the documentation
 */
$delivery->withAddressLine1('Address1')
    ->withAddressLine2('Address2')
    ->withCity('City')
    ->withCountryCode('RO')
    ->withEmail('john.doe@mail.com')
    ->withFirstName('FirstName')
    ->withLastName('LastName')
    ->withPhoneNumber('40123456789');

/**
 * Create new Request with params:
 *
 * Config object
 * Order object
 * Billing object
 * Delivery (or Billing object again, if you want to have the delivery address the same as the billing address)
 * User object
 */
$request = new Request($cfg, $order, $billing, $delivery, $user);

/**
 * Create new API Client, passing the Config object as parameter
 */
$client = new Client($cfg);

/**
 * Will throw different Exceptions on errors
 */
try {
    /**
     * Sends the Request to ALU and returns a Response
     *
     * See documentation for Response params
     */
    $response = $client->pay($request);

    echo $response->getReturnMessage() . ' ' . $response->getRefno() . ' ' . $response->getUrlRedirect();
} catch (ConnectionException $exception) {
    echo $exception->getMessage();
} catch (ClientException $exception) {
    echo $exception->getErrorMessage();
}
