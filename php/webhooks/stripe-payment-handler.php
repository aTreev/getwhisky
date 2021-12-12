<?php 
require_once("../../stripe/init.php");
require_once("../page.class.php");
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


// Handle the checkout.session.completed event
if ($event->type == 'checkout.session.completed') {
  $session = $event->data->object;
  // Fulfill the purchase...
  fulfill_order($session);
  
}

/********
 * Creates an order on the site database
 * the userid and cartid must be passed through 
 * the payment intent object as metadata
 * This is due to redirecting causing a new session in this file only
 ************************************/
function fulfill_order($session) {
  // get id's from metadata
  $cartid = $session->metadata->cartid;
  $userid = $session->metadata->userid;
  $addressid = $session->metadata->addressid;
  $deliveryid = $session->metadata->deliveryType;
  $stripePaymentIntent = $session->payment_intent;

  // begin checkout process with the metadata
  // passed to this file
  $page = new Page();
  $result = $page->createOrder($cartid, $addressid, $userid, $deliveryid, $stripePaymentIntent);
  print_log("RESULT: ".$result);
}

http_response_code(200);
?>