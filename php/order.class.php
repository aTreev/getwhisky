<?php
require_once("ordercrud.class.php");

class Order {

    private $orderid;
    private $userid;
    private $address;
    private $status;
    private $adminStatus;
    private $deliveryType;
    private $deliveryCost;
    private $stripePaymentIntent;
    private $datePlaced;
    private $total;

    public function __construct($orderid, $userid, $addressid, $status, $adminStatus, $deliveryType, $deliveryCost, $stripePaymentIntent, $datePlaced, $total) {
        // get order details and set them in this class
        // retrieve address
        // retrieve order items and send them to order_item array
        $this->setId($orderid);
        $this->setUserid($userid);
        $this->setStatus($status);
        $this->setAdminStatus($adminStatus);
        $this->setDeliveryType($deliveryType);
        $this->setDeliveryCost($deliveryCost);
        $this->setStripePaymentIntent($stripePaymentIntent);
        $this->setDatePlaced($datePlaced);
        $this->setTotal($total);
    }


    private function setId($orderid) { $this->orderid = $orderid; }
    private function setUserid($userid) { $this->userid = $userid; }
    private function setAddress($address) { $this->address = new UserAddress(); }
    private function setStatus($status) { $this->status = $status; }
    private function setAdminStatus($adminStatus) { $this->adminStatus = $adminStatus; }
    private function setDeliveryType($deliveryType) { $this->deliveryType = $deliveryType; }
    private function setDeliveryCost($deliveryCost) { $this->deliveryCost = $deliveryCost; }
    private function setStripePaymentIntent($stripePaymentIntent) { $this->stripePaymentIntent = $stripePaymentIntent; }
    private function setDatePlaced($datePlaced) { $this->datePlaced = $datePlaced; }
    private function setTotal($total) { $this->total = $total; }

    

    public function getOrderid(){ return $this->orderid; }
    public function getUserid(){ return $this->userid; }
    public function getAddress(){ return $this->address; }
    public function getStatus(){ return $this->status; }
    public function getAdminStatus(){ return $this->adminStatus; }
    public function getDeliveryType(){ return $this->deliveryType; }
    public function getDeliveryCost(){ return $this->deliveryCost; }
    public function getStripePaymentIntent(){ return $this->stripePaymentIntent; }
    public function getDatePlaced(){ return $this->datePlaced; }
    public function getTotal(){ return $this->total; }
}
?>