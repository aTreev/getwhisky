<?php 
require_once("cartcrud.class.php");
class Cartitem {
    private $cartId;    // id of the cart
    private $productId; // productId of the item
    private $quantity;  // Quantity of item in cart
    private $name;      // name of the product
    private $image;     // path to product image
    private $discounted;
    private $discountPrice;
    private $price;     // price of the product
    private $stock;     // product stock
    private $active;     // active state of the product


    public function __construct($item) {
        $this->setCartId($item['cart_id']);
        $this->setProductId($item['product_id']);
        $this->setQuantity($item['quantity']);

        // Get product details
        $this->retrieveItemDetails();
    }

    

    public function getCartId(){ return $this->cartId; }
    public function getProductId(){ return $this->productId; }
    public function getQuantity(){ return $this->quantity; }
    public function getName(){ return $this->name; }
    public function getImage(){ return $this->image; }
    public function getDiscounted(){ return $this->discounted; }
    public function getDiscountPrice(){ return $this->discountPrice; }
    public function getPrice(){ return $this->price; }
    public function getStock(){ return $this->stock; }
    public function isActive(){ return $this->active; }


    private function setCartId($cartId) { $this->cartId = $cartId; }
    private function setProductId($productId) { $this->productId = $productId; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }
    private function setName($name) { $this->name = $name; }
    private function setImage($image) { $this->image = $image; }
    private function setDiscounted($discounted){ $this->discounted = $discounted; }
    private function setDiscountPrice($discountPrice){ $this->discountPrice = $discountPrice; }
    private function setPrice($price) { $this->price = $price; }
    private function setStock($stock) { $this->stock = $stock; }
    private function setActive($activeState) { $this->active = $activeState; }


    private function retrieveItemDetails() {
        $source = new CartCRUD();
        $itemDetails = $source->getCartItemDetailsByProductId($this->getProductId());
        $this->setName($itemDetails[0]['name']);
        $this->setImage($itemDetails[0]['image']);
        $this->setDiscounted($itemDetails[0]['discounted']);
        $this->setDiscountPrice($itemDetails[0]['discount_price']);
        $this->setPrice($itemDetails[0]['price']);
        $this->setStock($itemDetails[0]['stock']);
        $this->setActive($itemDetails[0]['active']);
    }

    public function returnCorrectItemPrice() {
        if ($this->getDiscounted()) return $this->getDiscountPrice();
        return $this->getPrice();
    }

    
    public function __toString() {
        $html = "";
        $html.="<div class='cart-item' product-id='".$this->getProductId()."'>";
            $html.="<div class='cart-item-left'>";
                $html.="<div class='cart-item-image-name'>";
                    $html.="<img src='".$this->getImage()."'>";
                    $html.="<h3 id='product-name-".$this->getProductId()."'>".$this->getName()."</h3>";
                $html.="</div>";
            $html.="</div>";

            $html.="<div class='cart-item-right'>";
                $html.="<div class='cart-item-functions'>";
                    $html.="<input type='number' id='quantity-".$this->getProductId()."' value=".$this->getQuantity()."  max='".$this->getStock()."'>";
                $html.="</div>";

                $html.="<div class='cart-item-price-container'>";
                    if ($this->getDiscounted()) {
                        $html.="<div class='discounted-price-container'>";
                            $html.="<p>Unit price: </p>";
                            $html.="<p class='old-price'>£".$this->getPrice()."</p>";
                            $html.="<p class='new-price'>£".$this->getDiscountPrice()."</p>";
                        $html.="</div>";
                        $html.="<p>Subtotal:<span class='subtotal'> £".($this->getDiscountPrice()*$this->getQuantity())."</span></p>";
                    } else {
                        $html.="<p class='item-price'>Unit price: <span class='unit-price'>£".$this->getPrice()."</span></p>";
                        $html.="<p>Subtotal:<span class='subtotal'> £".($this->getPrice()*$this->getQuantity())."</span></p>";
                    }
                $html.="</div>";

                $html.="<button id='remove-".$this->getProductId()."' class='remove-from-cart-btn'>remove</button>";
            $html.="</div>";
        $html.="</div>";

        return $html;
    }
    
}
?>