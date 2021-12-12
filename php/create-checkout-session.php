<?php
require_once('../stripe/init.php');
require_once("page.class.php");

/****
 * The following delivery code is garbage due to being hard coded
 * Should be changed once delivery options are discussed with client
 *********/
if (util::valInt($_POST['deliveryType'], array(1,2)) && util::valStr($_POST['addressId'])) {
  // Retrieve posted arguments
  $deliveryType = $_POST['deliveryType'];
  $addressid = $_POST['addressId'];

  // Temporary hardcoded delivery logic
  if ($deliveryType == 1) {
    $deliveryLabel = "Standard Delivery Fee";
    $deliveryCost = 4.49;
  } 
  if ($deliveryType == 2) {
    $deliveryLabel = "First Class Delivery Fee";
    $deliveryCost = 5.99;
  } 

  /********
   * Initialize page on the same session as the user and retrieve
   * the required data
   *****************************/
  $page = new Page();
  $cart_items = $page->getCart()->getItems();
  $line_items = [];

  $cartid = $page->getCart()->getId();
  $userid = $page->getUser()->getUserid();

  // Format cart items to stripe line items
  foreach($cart_items as $item) {
    array_push($line_items, ['quantity' => $item->getQuantity(),'price_data' => ['currency' => 'gbp', 'product_data' => ['name' => $item->getName(),], 'unit_amount' => $item->returnCorrectItemPrice()*100]]);
  }
  // Add delivery cost to line items
  array_push($line_items, ['quantity' => 1,'price_data' => ['currency' => 'gbp', 'product_data' => ['name' => $deliveryLabel,], 'unit_amount' => $deliveryCost*100]]);


  // Validate that the address belongs to the user
  $userAddressCRUD = new UserAddressCRUD();
  $validAddress = $userAddressCRUD->getUserAddressById($addressid, $userid);

  if (!$validAddress) {
    header("Location: /deliveryselection.php");
    // send notification?
  }

  // All good, begin checkout
  beginStripeCheckout($line_items, $userid, $cartid, $addressid, $deliveryType);
} else {
  header("Location: /deliveryselection.php");
}


function beginStripeCheckout($line_items, $userid, $cartid, $addressid, $deliveryType) {
  // Stripe logic
  \Stripe\Stripe::setApiKey('sk_test_51Je0ufArTeMLOzQd1e4BFGLKWFOsabluGgErlDnWkmyea9G2LQQJY6PXusduRSaAXhsz6h27Owwz8n9SehfBY3a90087Gcb2ba');
  header('Content-Type: application/json');
  $DOMAIN = 'http://ecommercev2';
  $checkout_session = \Stripe\Checkout\Session::create([
      'billing_address_collection' => 'required',
      'line_items' => [$line_items],
    'payment_method_types' => [
      'card',
    ],
    'mode' => 'payment',
    // Add required fields for a checkout on the application as metadata
    'metadata' => [
      'userid' => $userid,
      'cartid' => $cartid,
      'addressid' => $addressid,
      'deliveryType' => $deliveryType
    ],
    'success_url' => $DOMAIN . '/success.php',
    'cancel_url' => $DOMAIN . '/cart.php',
  ]);
  header("HTTP/1.1 303 See Other");
  header("Location: " . $checkout_session->url);
}