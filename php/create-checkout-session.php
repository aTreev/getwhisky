<?php
require_once('../stripe/init.php');
// Push products here from database to prevent price manipulation
$line_items[0] = ['quantity' => '2','price_data' => ['currency' => 'gbp', 'product_data' => ['name' => 'Lagavulin 16 Year', 'images' => ['http://ecommercev2/assets/lagavulin-16-year-old-whisky.jpg']], 'unit_amount' => '6499']];
//array_push($line_items, ['quantity' => '1','price_data' => ['currency' => 'gbp', 'product_data' => ['name' => 'Lagavulin 16 Year',], 'unit_amount' => 64.99*100]]);

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
  'cancel_url' => $DOMAIN . '/checkout.php',
]);
header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);