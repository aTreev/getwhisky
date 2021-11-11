<?php
require_once("cartcrud.class.php");
require_once("cart-item.class.php");

class Cart {
    private $id;            // Id of the cart
    private $userid;        // Id of the user the cart belongs to
    private $checked_out;   // Whether the cart has been checked out or not
    private $items = [];    // Items in the cart -- retrieved during object initialization

    public function __construct($cart) {
        $this->setId($cart["id"]);
        $this->setUserid($cart["userid"]);
        $this->setCheckedOut($cart["checked_out"]);
        // get cart items
        $this->retrieveCartItems();
        // Check stock of items
        $this->checkItemStock();
    }

    

    public function getId(){ return $this->id; }
    public function getUserid(){ return $this->userid; }
    public function getCheckedOut(){ return $this->checked_out; }
    public function getItems(){ return $this->items; }
    public function getItem($index) { return $this->items[$index]; }

    private function setId($id) { $this->id = $id; }
    private function setUserid($userid) { $this->userid = $userid; }
    private function setCheckedOut($checked_out) { $this->checked_out = $checked_out; }
    private function setItems($items){ $this->items = $items; }


    /*****************
     * Retrieves the items in the cart from the database
     * pushes each item to the items array as a constructed object
     *********/
    private function retrieveCartItems() {
        $this->items = [];
        $source = new CartCRUD();
        $retrievedItems = $source->getCartItems($this->getId());

        if (count($retrievedItems) > 0) {
            foreach($retrievedItems as $item) {
                array_push($this->items, new CartItem($item));
            }
        }
    }


    /*********
     * Checks the stock of items in the cart and removes any that are out of stock
     * Also adjusts quantity if a quantity higher than available is in cart
     * Constructs and sends a notification to the $_SESSION which is called on every page
     * and handled via JavaScript
     **************************************************/
    private function checkItemStock() {
        $countItems = count($this->getItems());
        $itemsUpdated = 0;
        $itemsUpdatedDetails = [];
        $updatedHtml = "";

        // Increment through items
        for($i = 0; $i < $countItems; $i++) {
            // If item quantity greater than stock
            if ($this->getItem($i)->getQuantity() > $this->getItem($i)->getStock()) {
                if ($this->getItem($i)->getStock() == 0) {
                    $itemDetails = ['image' => $this->getItem($i)->getImage(), 'name' => $this->getItem($i)->getName(), 'action' => 'Removed from basket'];
                    $itemsUpdated++;
                    array_push($itemsUpdatedDetails, $itemDetails);
                    $this->removeFromCart($this->getItem($i)->getProductId());
                } else {
                    // update cart item quantity to stock
                    $this->updateCartItemQuantity($this->getItem($i)->getProductId(), $this->getItem($i)->getStock());
                    // Send item values back to array for notification
                    $itemsUpdated++;
                    $itemDetails = ['image' => $this->getItem($i)->getImage(), 'name' => $this->getItem($i)->getName(), 'action' => 'Quantity updated'];
                    array_push($itemsUpdatedDetails, $itemDetails);
                }
            }
        }
        // If any items updated construct update notification html
        if ($itemsUpdated > 0) {
            $updatedHtml.="<div style='top:120px;'class='cart-notification'>";
                $updatedHtml.="<div class='cart-notification-header'>";
                    $updatedHtml.="<p>Your basket has been updated due to item shortages</p>";
                    $updatedHtml.="<i class='fas fa-times' id='close-cart-notification'></i>";
                $updatedHtml.="</div>";
                foreach($itemsUpdatedDetails as $itemUpdatedDetails) {
                    $updatedHtml.="<div class='cart-notification-item'>";
                        $updatedHtml.="<img src='".$itemUpdatedDetails['image']."'>";
                        $updatedHtml.="<p>".$itemUpdatedDetails['name']." (".$itemUpdatedDetails['action'].")</p>";
                    $updatedHtml.="</div>";
                }
            $updatedHtml.="</div>";
            $_SESSION['cart-update-notification'] = $updatedHtml;
        }
    }

    /**********
     * Iterates through the cart's items and sums
     * the quantity of each item
     *************************/
    public function getCartItemCount() {
        $count = 0;
        foreach($this->getItems() as $item) {
            $count = $count + $item->getQuantity();
        }
        return $count;
    }



    /****
     * The next 4 methods are extremely inefficient and messy
     * TODO:    
     * Move stock and product existance checking to the 
     * page class that has access to all products
     ******************************/



    /**********
     * Checks that a product actually exists
     * used as a guard clause in other methods
     ********/
    private function checkProductExists($productId) {
        $testProduct = new CartCRUD();
        if (!$testProduct->checkProductExists($productId)) return 0;
        return 1;
    }

    /**************
     * Publicly accessible method to update the quantity of an item in cart
     * Updates the item on the database, if the query was successful, updates the
     * quantity in the item object
     * Returns different values depending on the result
     *  0   -   Generic Fail
     *  1   -   Successful update
     *  2   -   Insufficient quantity for update
     *  3   -   Invalid product id supplied
     ************/
    public function updateCartItemQuantity($productId, $quantity) {
        // Guard clause to check the product exists
        if (!$this->checkProductExists($productId)) return 3;
        $result = 0;
        $stock = 0;
        $itemFound = 0;

        // Check if item in cart and get stock
        foreach($this->getItems() as $item) {
            if ($item->getProductId() == $productId) {
                $stock = $item->getStock();
                $itemFound = 1;
                break;
            }
        }

        // Item not found
        if (!$itemFound) {
            $result =  4;
        } else {
            // Item found, check if stock available
            if ($stock >= $quantity) {
                $source = new CartCRUD();
                $result = $source->updateCartItemQuantity($this->getId(), $productId, $quantity);
            } else {
                $result = 2;
            }
        }

        // Succesfully updated, update in object
        if ($result == 1) {            
            foreach($this->getItems() as $item) {
                if ($item->getProductId() == $productId) {
                    $item->setQuantity($quantity);
                }
            }
        }
        return $result;
    }

    /***************
     * Searches through the cart's items to check if an item is already present
     * if the item is already present the quantity is incremented after a stock check
     * if the item is not present the product's stock is checked before adding new item to cart
     * Returns messages depending on result
     *  0   -   Generic fail
     *  1   -   Added to cart
     *  2   -   Insufficient stock
     *  3   -   Invalid product supplied
     ****************/
    public function addToCart($productId, $quantity) {
        // Guard clause to check the product exists
        if (!$this->checkProductExists($productId)) return 3;
        $result = 0;
        $inCart = 0;
        
        // Check if in cart and get quantity
        foreach($this->getItems() as $item) {
            if ($item->getProductId() == $productId) {
                $inCart = 1;
                $quantityInCart = $item->getQuantity();
                break;
            }
        }

        // already in cart update quantity
        if ($inCart) {
            $result = $this->updateCartItemQuantity($productId, $quantityInCart+$quantity);
            
        }

        // Not in cart add new cart item
        if (!$inCart) {
            // check stock greater than 0
            $insert = new CartCRUD();
            $stock = $insert->checkOutOfStock($productId)[0]['stock'];
            if ($stock == 0 || $stock < $quantity) {
                // out of stock error code
                $result = 2;
            } else {
                // add to cart
                $result = $insert->addToCart($this->getId(), $productId, $quantity);
            }
        }
        // if successful reload cart items
        if ($result == 1) {
            $this->retrieveCartItems();
        }
        return $result;
    }

    public function removeFromCart($productId) {
        // Guard clause to check the product exists
        if (!$this->checkProductExists($productId)) return 3;

        $source = new CartCRUD();
        $result = $source->removeFromCart($this->getId(), $productId);

        if ($result == 1) {
            // delete item from cart object
            $tmpItems = $this->getItems(); 
            $countItems = count($tmpItems);

            for($i = 0; $i < $countItems; $i++) {
                // loop through, remove item and set this->items as re-indexed array
                if ($tmpItems[$i]->getProductId() == $productId) {
                    unset($tmpItems[$i]);
                    $this->setItems(array_values($tmpItems));
                    break;
                }
            }
        }
        return $result;
    }

    public function __toString() {
        $html = "";
        if (count($this->getItems()) > 0) {
            // cart has items

            // call the __toString method of each cart item
            $html.="<div id='cart-item-root'>";
                foreach($this->getItems() as $item) {
                    $html.=$item;
                }
            $html.="</div>";

            // display cart summary
            $html.="<div id='cart-summary-root'>";
                $total = 0;
                foreach($this->getItems() as $item) {
                    $total = $total + ($item->returnCorrectPriceForTotal() * $item->getQuantity());
                }
                $html.="<h3>Basket Summary</h3>";
                $html.="<p>Total: £".$total."</p>";
                $html.="<button type='submit'>Checkout</submit>";
            $html."</div>";
        } else {
            // Display if empty cart
            $html.="<div class='no-items'>";
                $html.="<h3>Your shopping basket is empty!</h3>";
                $html.="<p>Add to your basket from our wide range of lovely drams and come back to complete your purchase!</p>";
                $html.="<a class='continue-shopping' href='/index.php'>Continue shopping</a>";
            $html.="</div>";
        }

        return $html;
    }
}

?>