<?php 
require_once("ordercrud.class.php");
class OrderItem {
    private $productid;
    private $productName;
    private $productImage;
    private $quantity;
    private $priceBought;


    public function __construct($productid, $productName, $productImage, $quantity, $priceBought) {
        $this->setProductid($productid);
        $this->setProductName($productName);
        $this->setProductImage($productImage);
        $this->setQuantity($quantity);
        $this->setPriceBought($priceBought);
    }

    public function setProductid($productid) { $this->productid = $productid; }
    public function setProductName($productName) { $this->productName = $productName; }
    public function setProductImage($productImage) { $this->productImage = $productImage; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }
    public function setPriceBought($priceBought) { $this->priceBought = $priceBought; }

    public function getProductid(){ return $this->productid; }
    public function getProductName(){ return $this->productName; }
    public function getProductImage(){ return $this->productImage; }
    public function getQuantity(){ return $this->quantity; }
    public function getPriceBought(){ return $this->priceBought; }
}
?>