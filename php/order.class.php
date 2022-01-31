<?php
require_once("ordercrud.class.php");
require_once("order-item.class.php");
require_once("user-address.class.php");

class Order {
    private $orderid;
    private $userid;
    private $address;
    private $status;
    private $adminStatus;
    private $deliveryLabel;
    private $deliveryCost;
    private $stripePaymentIntent;
    private $datePlaced;
    private $total;
    private $orderItems = [];

    public function __construct($order) {
        // get order details and set them in this class
        $this->setId($order['order_id']);
        $this->setUserid($order['userid']);
        $this->setStatus($order['status_label']);
        $this->setAdminStatus($order['admin_status_label']);
        $this->setDeliveryLabel($order['delivery_label']);
        $this->setDeliveryCost($order['delivery_paid']);
        $this->setStripePaymentIntent($order['stripe_payment_intent']);
        $this->setDatePlaced($order['date_placed']);
        $this->setTotal($order['total']);
        // retrieve address create new object with it
        // retrieve order items and send them to order_item array
        $this->retrieveOrderItems();
        $this->retrieveDeliveryAddress($order['address_id']);
    }


    private function setId($orderid) { $this->orderid = $orderid; }
    private function setUserid($userid) { $this->userid = $userid; }
    private function setAddress($addressObj) { $this->address = $addressObj; }
    private function setStatus($status) { $this->status = $status; }
    private function setAdminStatus($adminStatus) { $this->adminStatus = $adminStatus; }
    private function setDeliveryLabel($deliveryLabel) { $this->deliveryLabel = $deliveryLabel; }
    private function setDeliveryCost($deliveryCost) { $this->deliveryCost = $deliveryCost; }
    private function setStripePaymentIntent($stripePaymentIntent) { $this->stripePaymentIntent = $stripePaymentIntent; }
    private function setDatePlaced($datePlaced) { $this->datePlaced = $datePlaced; }
    private function setTotal($total) { $this->total = $total; }

    

    public function getOrderid(){ return $this->orderid; }
    public function getUserid(){ return $this->userid; }
    public function getAddress(){ return $this->address; }
    public function getStatus(){ return $this->status; }
    public function getAdminStatus(){ return $this->adminStatus; }
    public function getDeliveryLabel(){ return $this->deliveryLabel; }
    public function getDeliveryCost(){ return $this->deliveryCost; }
    public function getStripePaymentIntent(){ return $this->stripePaymentIntent; }
    public function getDatePlaced(){ return $this->datePlaced; }
    public function getTotal(){ return $this->total; }
    public function getOrderItems(){ return $this->orderItems; }

    // Converts and returns UK formatted date
    private function getFormattedDate() {
        return date("d M Y", strtotime($this->getDatePlaced()));
    }

    private function displayDeliveryAddress() {
        $html = "";
        $html.="<span class='delivery-address'>";
            $html.=$this->getAddress()->getFullName().", ";
            $html.=$this->getAddress()->getLine1().", ";
            if ($this->getAddress()->getLine2()) $html.=$this->getAddress()->getLine2().", ";
            $html.=$this->getAddress()->getPostcode().", ";
            $html.=$this->getAddress()->getCity().", ";
            $html.=$this->getAddress()->getCounty();
        $html.="</span>";
        return $html;
    }

    private function retrieveOrderItems() {
        $source = new OrderCRUD();
        $items = $source->getOrderItems($this->getOrderid());
        foreach($items as $item) {
            array_push($this->orderItems, new OrderItem($item['product_id'], $item['name'], $item['image'], $item['quantity'], $item['price_bought']));
        }
    }

    private function retrieveDeliveryAddress($addressid) {
        $source = new UserAddressCRUD();
        $addressAssoc = $source->getUserAddressById($addressid, $this->getUserid());
        foreach($addressAssoc as $address) {
            $this->setAddress(new UserAddress($address));
        }
    }


    // Displays the user view of the order page
    public function displayUserOrderPage() {
        $html = "";

        $html.="<div class='order'>";
            $html.="<div class='order-header-container'>";
                $html.="<h4>Order #".$this->getOrderid()." - Placed on ".$this->getFormattedDate()."</h4>";
            $html.="</div>";

            $html.="<div class='order-details-container'>";
                $html.="<ul>";
                    $html.="<li><b>Order ID:</b> ".$this->getOrderid()."</li>";
                    $html.="<li><b>Payment reference:</b> ".$this->getStripePaymentIntent()."</li>";

                    $html.="<li><b>Status:</b> <span class='".strtolower($this->getStatus())."'>".$this->getStatus()."</span></li>";
                    $html.="<li><b>Total:</b> £".($this->getTotal() + $this->getDeliveryCost())."</li>";
                    $html.="<li><b>Delivery type:</b> ".$this->getDeliveryLabel()." £".$this->getDeliveryCost()."</li>";
                    $html.="<li><b>Delivery address:</b> ".$this->displayDeliveryAddress()."</li>";
                $html.="</ul>";

            $html.="</div>";

            $html.="<div class='order-items'>";
                foreach($this->getOrderItems() as $item) {
                    $html.="<div class='order-item'>";
                        $html.="<a href='/productpage.php?pid=".$item->getProductid()."'><img style='width:100px;' src='".$item->getProductImage()."'/></a>";
                        $html.="<a href='/productpage.php?pid=".$item->getProductid()."'><h4>".$item->getProductName()."</h4></a>";
                        $html.="<p><b>QTY:</b> ".$item->getQuantity()."</p>";
                        $html.="<p><b>Price:</b> £".$item->getPriceBought()."</p>";
                    $html.="</div>";
                }
            $html.="</div>";
        $html.="</div>";

        return $html;
    }


    public function displayOrderAdmin() {
        $html = "";
        $html.="<tr status='".strtolower($this->getStatus())."' orderid='".$this->getOrderid()."' name='order'>";
            $html.="<td>".$this->getAddress()->getFullName()."</td>";
            $html.="<td>#".$this->getOrderid()."</td>";
            $html.="<td>".$this->getStripePaymentIntent()."</td>";
            $html.="<td>".$this->getFormattedDate()."</td>";
            $html.="<td>£".$this->getTotal()."</td>";
            $html.="<td>".$this->getDeliveryLabel()."</td>";

            $html.="<td class='".strtolower($this->getStatus())."'>".$this->getStatus()."</td>";
            $html.="<td>";
                $html.="<div class='table-flex-row'>";
                if ($this->getStatus() == "Processing") {
                    $html.="<button class='btn-dispatch' id='set-order-dispatched-".$this->getOrderid()."'>Dispatched</button>";
                }
                $html.="<button class='btn-view-items' id='view-order-items-".$this->getOrderid()."'>View Items</button>";
                $html.="</div>";
            $html.="</td>";
            $html.="<div class='order-items'>";
                $html.="<p>order items</p>";
            $html.="</div>";

            $html.="<tr name='order-items-".$this->getOrderid()."' class='order-items-row'>";
                $html.="<td colspan='7'>";
                    $html.="<div class='order-items-container'>";
                        foreach ($this->getOrderItems() as $item) {
                            $html.=$item->displayOrderItemUser();
                        }
                    $html.="</div>";
                $html.="</td>";
            $html.="</tr>";
        $html.="</tr>";
        return $html;
    }
}
?>