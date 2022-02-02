<?php 
require_once("ordercrud.class.php");
require_once("useraddresscrud.class.php");
class Emailer {
    private $recipient;
    private $sender;
    private $subject;
    private $headers = "";
    private $emailMarkup = "";


    public function __construct($recipient, $sender, $subject) {
        $this->setRecipient($recipient);
        $this->setSender($sender);
        $this->setSubject($subject);
        $this->headers.= "From: ".$sender."\r\n";
        $this->headers.="MIME-Version: 1.0\r\n";
        $this->headers.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    }


    private function setRecipient($recipient) { $this->recipient = $recipient; }
    private function setSender($sender) { $this->sender = $sender; }
    private function setSubject($subject) { $this->subject = $subject; }

    public function getRecipient() { return $this->recipient; }
    public function getSender() { return $this->sender; }
    public function getSubject() { return $this->subject; }
    public function getHeaders() { return $this->headers; }
    public function getEmailMarkup() { return $this->emailMarkup; }

    private function sendConstructedEmail() {
        mail($this->getRecipient(), $this->getSubject(), $this->getEmailMarkup(), $this->getHeaders());
    }

    public function sendOrderConfirmationEmail($orderid) {
        $orderCRUD = new OrderCRUD();
		$addressCRUD = new UserAddressCRUD();

		$orderDetails = $orderCRUD->getOrderById($orderid);
		$orderItems = $orderCRUD->getOrderItems($orderid);
		$addressDetails = $addressCRUD->getUserAddressById($orderDetails[0]['address_id'], $orderDetails[0]['userid']);

        $this->emailMarkup= "";
        $this->emailMarkup.="<!DOCTYPE html>";
        $this->emailMarkup.="<html lang='en'>";
        $this->emailMarkup.="<head>";
            $this->emailMarkup.="<meta charset='UTF-8'>";
            $this->emailMarkup.="<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
            $this->emailMarkup.="<title>Document</title>";
        $this->emailMarkup.="</head>";
        $this->emailMarkup.="<body style='max-width:600px;margin:auto;border: 1px solid rgb(221, 220, 220);'>";
            // heading div
            $this->emailMarkup.="<div style='padding: 10px 20px;background-color: rgb(51,49,49);padding-bottom:10px;'>";
                $this->emailMarkup.="<h1 style='text-align:left;color:white;font-weight:300;font-family:sans-serif;letter-spacing: 0.1ch;font-size:24px'>getwhisky order confirmation</h1>";
            $this->emailMarkup.="</div>";

            // Personalised thank you
            $this->emailMarkup.="<div style='line-height: 1.2;padding: 10px;font-family: sans-serif;border-bottom: 1px solid rgb(221, 220, 220);padding:15px 10px;'>";
                $this->emailMarkup.="<p style='font-size:14px;margin-bottom: 10px;'>Hi ".$addressDetails[0]['full_name']."</p>";
                $this->emailMarkup.="<p style='font-size:14px;'>Thank you for your recent order at the getwhisky store!</p>";
                $this->emailMarkup.="<p style='font-size:14px;'>We'll follow up with another email once your items have been dispatched.</p>";
                $this->emailMarkup.="<p style='font-size:14px;'>For now, please find the details of your order below</p>";
            $this->emailMarkup.="</div>";
        
            // Order details and delivery address
            $this->emailMarkup.="<div style='padding: 0px 10px 20px 10px;border-bottom: 1px solid rgb(221, 220, 220);'>";
                $this->emailMarkup.="<h3 style='font-weight:400;font-size:20px;'>Summary</h3>";
                $this->emailMarkup.="<table style='width: 100%;'>";
                    $this->emailMarkup.="<tr>";
                        $this->emailMarkup.="<td style='padding-bottom: 10px;font-size:16px;font-style:italic;'>";
                            $this->emailMarkup.="Order details";
                        $this->emailMarkup.="</td>";
                        $this->emailMarkup.="<td style='padding-bottom: 10px;font-size:16px;font-style:italic;'>";
                            $this->emailMarkup.="We'll deliver to ";
                        $this->emailMarkup.="</td>";
                    $this->emailMarkup.="</tr>";
                    $this->emailMarkup.="<tr>";
                        // Order details
                        $this->emailMarkup.="<td style='font-size:14px;line-height: 1.6;'>";
                            $this->emailMarkup.="<b>Order number: </b>#".$orderDetails[0]['order_id']."<br>";;
                            $this->emailMarkup.="<b>Date: </b>".date("d M Y",strtotime($orderDetails[0]['date_placed']))."<br>";
                            $this->emailMarkup.="<b>Total: </b>&#163;".($orderDetails[0]['total'])."<br>";
                            $this->emailMarkup.="<b>Delivery Type: </b>".$orderDetails[0]['delivery_label']." &#163;".$orderDetails[0]['delivery_paid']."<br>";
                        $this->emailMarkup.="</td>";
                        // Delivery address
                        $this->emailMarkup.="<td style='font-size:14px;line-height: 1.6;'>";
                            $this->emailMarkup.=$addressDetails[0]['full_name']."<br>";
                            $this->emailMarkup.=$addressDetails[0]['line1']."<br>";
                            if ($addressDetails[0]['line2']) $this->emailMarkup.=$addressDetails[0]['line2']."<br>";
                            $this->emailMarkup.=$addressDetails[0]['postcode']."<br>";
                            if ($addressDetails[0]['city'])$this->emailMarkup.=$addressDetails[0]['city']."<br>";
                            if ($addressDetails[0]['county'])$this->emailMarkup.=$addressDetails[0]['county'];
                        $this->emailMarkup.="</td>";
                    $this->emailMarkup.="</tr>";
                $this->emailMarkup.="</table>";
            $this->emailMarkup.="</div>";

            // Order items and total
            $this->emailMarkup.="<div style='padding-top:20px;'>";
                $this->emailMarkup.="<h3 style='padding-left:10px;padding-bottom:15px;font-size:20px;font-weight:400;'>Items to be delivered</h3>";
                $this->emailMarkup.="<table style='width: 100%;border-collapse:collapse;'>";
                    $this->emailMarkup.="<tr style='font-size:16px;background-color: rgb(51, 49, 49);color:white;'><td style='padding: 15px;'>Product </td><td style='padding: 15px;'>Qty</td><td style='padding: 15px;'>Unit Price</td><td style='padding: 15px;'>Subtotal</td></tr>";
                    foreach($orderItems as $item) {
                        $this->emailMarkup.="<tr style='border-bottom: 1px solid rgb(221, 220, 220);'>";
                        $this->emailMarkup.="<td style='padding: 15px;'>".$item['name']."</td>";
                        $this->emailMarkup.="<td style='padding: 15px;'>".$item['quantity']."</td>";
                        $this->emailMarkup.="<td style='padding: 15px;'>&#163;".$item['price_bought']."</td>";
                        $this->emailMarkup.="<td style='padding: 15px;'>&#163;".($item['quantity'] * $item['price_bought'])."</td>";
                        $this->emailMarkup.="</tr>";
                    }
                    $this->emailMarkup.="<tr style='border-bottom: 1px solid rgb(221, 220, 220);'><td style='padding: 20px;' colspan=3><b style='font-size:16px;'>Total:</b></td><td><b style='font-size:16px;'>&#163;".$orderDetails[0]['total']."</b></td></tr>";
                $this->emailMarkup.="</table>";
            $this->emailMarkup.="</div>";

            // Additional Info
            $this->emailMarkup.="<div style='padding: 20px 10px;'>";
                $this->emailMarkup.="<table style='width: 100%;'>";
                    $this->emailMarkup.="<tr>";
                        $this->emailMarkup.="<td style='font-size:14px;line-height: 1.6;'>Your order details can also be seen on the order section of your account.<br>Additionally, if you have any issues with your order please email us at <a href='mailto:info@getwhisky.site' style='font-size:14px;color:rgb(51, 49, 49)'>info@getwhisky.site</a></td>";
                    $this->emailMarkup.="</tr>";
                $this->emailMarkup.="</table>";
            $this->emailMarkup.="</div>";
        $this->emailMarkup.="</body>";
        $this->emailMarkup.="</html>";

        $this->sendConstructedEmail();
    }

    public function sendOrderDispatchedEmail($orderid) {
        $orderCRUD = new OrderCRUD();
		$addressCRUD = new UserAddressCRUD();

		$orderDetails = $orderCRUD->getOrderById($orderid);
		$orderItems = $orderCRUD->getOrderItems($orderid);
		$addressDetails = $addressCRUD->getUserAddressById($orderDetails[0]['address_id'], $orderDetails[0]['userid']);

        $this->emailMarkup= "";
        $this->emailMarkup.="<!DOCTYPE html>";
        $this->emailMarkup.="<html lang='en'>";
        $this->emailMarkup.="<head>";
            $this->emailMarkup.="<meta charset='UTF-8'>";
            $this->emailMarkup.="<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
            $this->emailMarkup.="<title>Document</title>";
        $this->emailMarkup.="</head>";
        $this->emailMarkup.="<body style='max-width:600px;margin:auto;border: 1px solid rgb(221, 220, 220);'>";
            // heading div
            $this->emailMarkup.="<div style='padding: 10px 20px;background-color: rgb(51,49,49);padding-bottom:10px;'>";
                $this->emailMarkup.="<h1 style='text-align:left;color:white;font-weight:300;font-family:sans-serif;letter-spacing: 0.1ch;font-size:24px'>getwhisky order #".$orderDetails[0]['order_id']." dispatched</h1>";
            $this->emailMarkup.="</div>";
            // why this email div
            $this->emailMarkup.="<div style='line-height: 1.2;padding: 10px;font-family: sans-serif;border-bottom: 1px solid rgb(221, 220, 220);padding:15px 10px;'>";
                $this->emailMarkup.="<p style='font-size:14px;margin-bottom: 10px;'>Hi ".$addressDetails[0]['full_name']."</p>";
                $this->emailMarkup.="<p style='font-size:14px;'>Thank you for shopping with us!</p>";
                $this->emailMarkup.="<p style='font-size:14px;'>Your order has now been dispatched and is on it's way to you.</p>";
                $this->emailMarkup.="<p style='font-size:14px;'>If you have any issues with this order please get in touch at <a href='mailto:info@getwhisky.site' style='font-size:14px;color:rgb(51, 49, 49);'>info@getwhisky.site</a></p>";
            $this->emailMarkup.="</div>";

            // Order items
            $this->emailMarkup.="<div style='padding-top:20px;'>";
                $this->emailMarkup.="<h3 style='padding-left:10px;padding-bottom:15px;font-size:20px;font-weight:400;'>Items to be delivered</h3>";
                $this->emailMarkup.="<table style='width: 100%;border-collapse:collapse;'>";
                    $this->emailMarkup.="<tr style='font-size:16px;background-color: rgb(51, 49, 49);color:white;'><td style='padding: 15px;'>Product</td><td style='padding: 15px;'>Qty</td></tr>";
                    foreach($orderItems as $item) {
                        $this->emailMarkup.="<tr style='border-bottom: 1px solid rgb(221, 220, 220);'>";
                        $this->emailMarkup.="<td style='padding: 15px;'>".$item['name']."</td>";
                        $this->emailMarkup.="<td style='padding: 15px;'>".$item['quantity']."</td>";
                        $this->emailMarkup.="</tr>";
                    }
                $this->emailMarkup.="</table>";
            $this->emailMarkup.="</div>";

            $this->emailMarkup.="</body>";
        $this->emailMarkup.="</html>";

        $this->sendConstructedEmail();
    }


    public function sendRefundEmail($orderid, $amountRefunded) {
        $orderCRUD = new OrderCRUD();
		$addressCRUD = new UserAddressCRUD();

		$orderDetails = $orderCRUD->getOrderById($orderid);
		$addressDetails = $addressCRUD->getUserAddressById($orderDetails[0]['address_id'], $orderDetails[0]['userid']);

        $this->emailMarkup= "";
        $this->emailMarkup.="<!DOCTYPE html>";
        $this->emailMarkup.="<html lang='en'>";
        $this->emailMarkup.="<head>";
            $this->emailMarkup.="<meta charset='UTF-8'>";
            $this->emailMarkup.="<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
            $this->emailMarkup.="<title>Document</title>";
        $this->emailMarkup.="</head>";
        $this->emailMarkup.="<body style='max-width:600px;margin:auto;border: 1px solid rgb(221, 220, 220);'>";
            // heading div
            $this->emailMarkup.="<div style='padding: 10px 20px;background-color: rgb(51,49,49);padding-bottom:10px;'>";
                $this->emailMarkup.="<h1 style='text-align:left;color:white;font-weight:300;font-family:sans-serif;letter-spacing: 0.1ch;font-size:24px'>getwhisky order #".$orderDetails[0]['order_id']."</h1>";
            $this->emailMarkup.="</div>";
            // why this email div
            $this->emailMarkup.="<div style='line-height: 1.2;padding: 10px;font-family: sans-serif;border-bottom: 1px solid rgb(221, 220, 220);padding:15px 10px;'>";
                $this->emailMarkup.="<p style='font-size:14px;margin-bottom: 10px;'>Hi ".$addressDetails[0]['full_name']."</p>";
                $this->emailMarkup.="<p style='font-size:14px;'>We're writing to let you know that you have been refunded for the amount of &#163;$amountRefunded.</p>";
                $this->emailMarkup.="<p style='font-size:14px;'>This refund should appear in your account within 5-7 business days.</p>";
                $this->emailMarkup.="<p style='font-size:14px;'>If you have any further issues please get in touch at <a href='mailto:info@getwhisky.site' style='font-size:14px;color:rgb(51, 49, 49);'>info@getwhisky.site</a></p>";
            $this->emailMarkup.="</div>";
        $this->emailMarkup.="</body>";
        $this->emailMarkup.="</html>";

        $this->sendConstructedEmail();
    }
}
?>