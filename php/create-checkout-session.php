<?php
require_once('../stripe/init.php');
require_once("page.class.php");

$page = new Page();
// retrieve user's cart items
$cart_items = $page->getCart()->getItems();
$line_items = [];

// Format cart items to stripe line items
foreach($cart_items as $item) {
  array_push($line_items, ['quantity' => $item->getQuantity(),'price_data' => ['currency' => 'gbp', 'product_data' => ['name' => $item->getName(),], 'unit_amount' => $item->returnCorrectPriceForTotal()*100]]);
}

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
  'success_url' => $DOMAIN . '/success.php',
  'cancel_url' => $DOMAIN . '/cart.php',
]);
header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);