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

    private function setCartId($cartId) { $this->cartId = $cartId; }
    private function setProductId($productId) { $this->productId = $productId; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }
    private function setName($name) { $this->name = $name; }
    private function setImage($image) { $this->image = $image; }
    private function setDiscounted($discounted){ $this->discounted = $discounted; }
    private function setDiscountPrice($discountPrice){ $this->discountPrice = $discountPrice; }
    private function setPrice($price) { $this->price = $price; }

    private function retrieveItemDetails() {
        $source = new CartCRUD();
        $itemDetails = $source->getCartItemDetailsByProductId($this->getProductId());
        $this->setName($itemDetails[0]['name']);
        $this->setImage($itemDetails[0]['image']);
        $this->setDiscounted($itemDetails[0]['discounted']);
        $this->setDiscountPrice($itemDetails[0]['discount_price']);
        $this->setPrice($itemDetails[0]['price']);
    }

    public function returnCorrectPriceForTotal() {
        if ($this->getDiscounted()) return $this->getDiscountPrice();
        return $this->getPrice();
    }

    
    public function __toString() {
        $html = "";
        $html.="<div class='cart-item'>";
            $html.="<div class='cart-item-left'>";
                $html.="<img src='".$this->getImage()."'>";
            $html.="</div>";
            $html.="<div class='cart-item-right'>";
                $html.="<h3>".$this->getName()."</h3>";

                $html.="<div class='cart-item-functions'>";
                    $html.="<div class='cart-item-quantity-container'>";
                        $html.="<label for='quantity'>Quantity</label>";
                        $html.="<input type='number' name='quantity' value='".$this->getQuantity()."'>";
                        $html.="<input type='hidden' name='product_id' value='".$this->getProductId()."'>";
                    $html.="</div>";
                    $html.="<div class='cart-item-buttons'>";
                        $html.="<a href='#' name='update-qty'>Update</a>";
                        $html.="<a href='#'name='remove-from-cart'>Remove</a>";
                    $html.="</div>";
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
                        $html.="<p class='item-price'>Unit price: £".$this->getPrice()."</p>";
                        $html.="<p>Subtotal:<span class='subtotal'> £".($this->getPrice()*$this->getQuantity())."</span></p>";
                    }
                $html.="</div>";
            $html.="</div>";
        $html.="</div>";

        return $html;
    }
    
}
?>