<?php
require 'vendor/autoload.php';


\Stripe\Stripe::setApiKey('sk_test_51JGPBgSC4yc2oxfAcCcecPqbWG1iFUrIwei4aFsS7oB1kJpbrA1wgGSsWSadsPd8abQfhw3xc1WxRJQ2fgLyoyHW00i7lATrQL');


$logFile = 'stripe_payment_log.txt';


$token = $_POST['stripeToken'];
$productId = $_POST['item_id'];
$productName = $_POST['item_name'];
$productDescription = $_POST['item_description'];
$priceInDollars = $_POST['price'];
$priceInCents = $priceInDollars * 100;

try {

    $customer = \Stripe\Customer::create([
        'source' => $token,
        'description' => "Customer for product: $productName",
    ]);


    $charge = \Stripe\Charge::create([
        'customer' => $customer->id,
        'amount' => $priceInCents,
        'currency' => 'usd',
        'description' => "Payment for $productName (ID: $productId)",
        'metadata' => [
            'product_id' => $productId,
            'product_name' => $productName,
            'product_description' => $productDescription,
        ],
    ]);

  
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Payment Successful: $productName, ID: $productId, Amount: $priceInDollars USD\n", FILE_APPEND);

   
    echo '<h1>Payment Successful!</h1>';
    echo '<p>Thank you for your purchase of ' . $productName . '.</p>';

} catch (\Stripe\Exception\CardException $e) {
   
    echo '<h1>Payment Failed!</h1>';
    echo '<p>There was an issue processing your payment: ' . $e->getMessage() . '</p>';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Payment Failed: $productName, Error: " . $e->getMessage() . "\n", FILE_APPEND);
}
?>
