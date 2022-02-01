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

        $this->emailMarkup.="<div style='margin:auto;font-family:sans-serif;'>";
            $this->emailMarkup.="<p style='margin-left:10px;margin-right:10px;font-size:30px;'>Hi ".$addressDetails[0]['full_name']."</p>";
            $this->emailMarkup.="<p style='margin-left:10px;margin-right:10px;font-size:30px;'>Thank you for your recent purchase on getwhisky.site.</p>";
            $this->emailMarkup.="<p style='margin-left:10px;margin-right:10px;padding-bottom:60px;font-size:30px;'>Please find your order summary below.</p>";

            $this->emailMarkup.="<div style='background-color:#ededed;padding:10px 20px 10px 20px;line-height:0.8;display:flex;justify-content:space-between'>";
                $this->emailMarkup.="<div>";
                    $this->emailMarkup.="<h3 style='font-size:18px;'>Order details</h3>";
                    $this->emailMarkup.="<p style='font-size:14px;'><b>Order ID: </b>".$orderDetails[0]['order_id']."</p>";
                    $this->emailMarkup.="<p style='font-size:14px;'><b>Payment ref: </b>".$orderDetails[0]['stripe_payment_intent']."</p>";
                    $this->emailMarkup.="<p style='font-size:14px;'><b>Order date: </b>".date("d M Y",strtotime($orderDetails[0]['date_placed']))."</p>";
                    $this->emailMarkup.="<p style='font-size:14px;'><b>Total: </b>&#163;".($orderDetails[0]['total']+$orderDetails[0]['delivery_paid'])."</p>";
                    $this->emailMarkup.="<p style='font-size:14px;'><b>Delivery Option: </b>".$orderDetails[0]['delivery_label']." &#163;".$orderDetails[0]['delivery_paid']."</p>";
                $this->emailMarkup.="</div>";
                $this->emailMarkup.="<div style='padding:40px 0px 0px 0px;'>";
                    $this->emailMarkup.="<h3 style='font-size:18px;'>Delivery address</h3>";
                    $this->emailMarkup.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['full_name']."</p>";
                    $this->emailMarkup.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['line1']."</p>";
                    $this->emailMarkup.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['line2']."</p>";
                    $this->emailMarkup.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['postcode']."</p>";
                    $this->emailMarkup.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['city']."</p>";
                    $this->emailMarkup.="<p style='opacity:0.8;font-size:14px;'>".$addressDetails[0]['county']."</p>";
                $this->emailMarkup.="</div>";
            $this->emailMarkup.="</div>";

            $this->emailMarkup.="<div style='background-color:#ededed;padding:40px 20px 10px 20px;line-height:0.8;'>";
                $this->emailMarkup.="<h3 style='font-size:18px;padding:0;margin:0;padding-bottom:20px;'>Items to be delivered</h3>";
                foreach($orderItems as $item) {
                    $this->emailMarkup.="<div style='display:flex;justify-content:space-between;border-bottom:1px solid black;'>";
                        $this->emailMarkup.="<div>";
                            $this->emailMarkup.="<p style='font-size:14px;font-weight:600;'>".$item['name']."</p>";
                            $this->emailMarkup.="<p style='opacity:0.8;font-size:14px;'>Quantity: ".$item['quantity']."<p>";
                            $this->emailMarkup.="<p style='opacity:0.8;font-size:14px;'>Price unit: &#163;".$item['price_bought']."</p>";
                        $this->emailMarkup.="</div>";

                        $this->emailMarkup.="<div style='display:flex;align-items:flex-end;'>";
                            $this->emailMarkup.="<p style='font-size:14px;font-weight:600;'> &#163;".($item['quantity'] * $item['price_bought'])."</p>";
                        $this->emailMarkup.="</div>";
                    $this->emailMarkup.="</div>";
                }
            $this->emailMarkup.="</div>";

            $this->emailMarkup.="<div style='background-color:#ededed;padding:40px 20px 10px 20px;line-height:1.2;'>";
                $this->emailMarkup.="<p style='font-size:14px;opacity:0.8;'>If you have any issues with your order please give us a call on 011114411. Alternatively email us at info@getwhisky.com</p>";
                $this->emailMarkup.="<p style='font-size:14px;opacity:0.8;'>These details can be viewed through the orders section on your account if you have created an account with us. If not please consider registering with us <a href='http://ecommercev2/register.php'>here!</a></p>";
            $this->emailMarkup.="</div>";
        $this->emailMarkup.="</div>";

        $this->sendConstructedEmail();
    }
}
?>