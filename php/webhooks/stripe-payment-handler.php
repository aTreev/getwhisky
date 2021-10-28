<?php 
require_once("../stripe/init.php");
require_once("../php/page.class.php");
\Stripe\Stripe::setApiKey('sk_test_51Je0ufArTeMLOzQd1e4BFGLKWFOsabluGgErlDnWkmyea9G2LQQJY6PXusduRSaAXhsz6h27Owwz8n9SehfBY3a90087Gcb2ba');

function print_log($val) {
  return file_put_contents('php://stderr', print_r($val, TRUE));
}

// You can find your endpoint's secret in your webhook settings
$endpoint_secret = 'whsec_xDUKJK6xkZM7kI7UWAFInvAcTPPXtFz2';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $endpoint_secret
  );
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  http_response_code(400);
  exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
  // Invalid signature
  http_response_code(400);
  exit();
}

/*********
 * FULFILL ORDERS HERE
 **************************************/
switch ($event->type) {
case 'checkout.session.completed':
    $session = $event->data->object;

    // Save an order in your database, marked as 'awaiting payment'
        //create_order($session);
        $payment_intent = $session->payment_intent; // store this alongside orderid etc to allow for refundsstr
        print_log("PAYMENT INTENT: ".$session->payment_intent);

    // Check if the order is paid (e.g., from a card payment)
    //
    // A delayed notification payment will have an `unpaid` status, as
    // you're still waiting for funds to be transferred from the customer's
    // account.
    if ($session->payment_status == 'paid') {
    // Fulfill the purchase
        //fulfill_order($session);
    }

    break;

case 'checkout.session.async_payment_succeeded':
    $session = $event->data->object;

    // Fulfill the purchase
    //fulfill_order($session);

    break;

case 'checkout.session.async_payment_failed':
    $session = $event->data->object;

    // Send an email to the customer asking them to retry their order
    //email_customer_about_failed_payment($session);

    break;
}

http_response_code(200);
?>