<?php 
require_once("productcrud.class.php");

class Product {
    private $id;
    private $name;
    private $heading;
    private $description;
    private $image;
    private $price;
    private $discounted;
    private $discountPrice;
    private $discountEndDatetime;
    private $stock;
    private $purchases;
    private $active;
    private $dateAdded;
    private $alcoholVolume;
    private $bottleSize;
    private $type;
    private $categoryId;
    private $attributes = [];
    private $featured;

    public function __construct($product){
        $this->setId($product['id']);
        $this->setName($product['name']);
        $this->setHeading($product['heading']);
        $this->setImage($product['image']);
        $this->setPrice($product['price']);
        $this->setDescription($product['description']);
        $this->setDiscounted($product['discounted']);
        $this->setDiscountPrice($product['discount_price']);
        $this->setDiscountEndDatetime($product['discount_end_datetime']);
        $this->setStock($product['stock']);
        $this->setPurchases($product['purchases']);
        $this->setActive($product['active']);
        $this->setDateAdded($product['date_added']);
        $this->setAlcoholVolume($product['alcohol_volume']);
        $this->setBottleSize($product['bottle_size']);
        $this->setType($product['type']);
        $this->setCategoryId($product['category_id']);
        $this->setFeatured($product['featured']);

        // Retrieve the attribute_value ids for product filter
        $this->retrieveAttributeValueIds();

        // If a discount is active check when it ends
        if ($this->isDiscounted()) {
            $this->checkDiscountEnded();
        }
    }
    
    public function getId(){ return $this->id; }
    public function getName(){ return $this->name; }
    public function getHeading(){ return $this->heading; }
    public function getDescription(){ return $this->description; }
    public function getImage(){ return $this->image; }
    public function getPrice(){ return $this->price; }
    public function isDiscounted(){ return $this->discounted; }
    public function getDiscountPrice(){ return $this->discountedPrice; }
    public function getDiscountEndDatetime(){ return $this->discountEndDatetime; }
    public function getStock(){ return $this->stock; }
    public function getPurchases(){ return $this->purchases; }
    public function isActive(){ return $this->active; }
    public function getDateAdded(){ return $this->dateAdded; }
    public function getAlcoholVolume(){ return $this->alcoholVolume; }
    public function getBottleSize(){ return $this->bottleSize; }
    public function getType(){ return $this->type; }
    public function getCategoryId(){ return $this->categoryId; }
    public function getAttributes(){ return $this->attributes; }
    public function isFeatured() { return $this->featured; }

    private function setId($id) { $this->id = $id; }
    private function setName($name) { $this->name = $name; }
    private function setHeading($heading) { $this->heading = $heading; }
    private function setDescription($description) { $this->description = $description; }
    private function setImage($image) { $this->image = $image; }
    private function setPrice($price) { $this->price = $price; }
    private function setDiscounted($discounted) { $this->discounted = $discounted; }
    private function setDiscountPrice($discountedPrice) { $this->discountedPrice = $discountedPrice; }
    private function setDiscountEndDatetime($discountEndDatetime) { $this->discountEndDatetime = $discountEndDatetime; }
    private function setStock($stock) { $this->stock = $stock; }
    private function setPurchases($purchases) { $this->purchases = $purchases; }
    private function setActive($active) { $this->active = $active; }
    private function setDateAdded($dateAdded) { $this->dateAdded = $dateAdded; }
    private function setAlcoholVolume($alcoholVolume) { $this->alcoholVolume = $alcoholVolume; }
    private function setBottleSize($bottleSize) { $this->bottleSize = $bottleSize; }
    private function setType($type) { $this->type = $type; }
    private function setCategoryId($categoryId) { $this->categoryId = $categoryId; }
    private function setAttributes($attributes) { $this->attributes = $attributes; }
    public function setFeatured($featured) { $this->featured = $featured; }



    /***********
     * Checks whether the set discount date has passed
     * and ends the discount if it has
     ****************/
    private function checkDiscountEnded() {
        if (strtotime($this->getDiscountEndDatetime()) <= time()) {
            $this->setDiscounted(false);
            $this->setDiscountEndDatetime(null);
            $this->setDiscountPrice(null);

            $source = new ProductCRUD();
            $source->endProductDiscount($this->getId());
        }
    }

    private function getActivePrice() {
        if ($this->isDiscounted()) return $this->getDiscountPrice();
        else return $this->getPrice();
    }

    /**********
     * Calculates and returns the percentage discount
     ****************************/
    private function getDiscountPercentage() {
        return floor((($this->getPrice() - $this->getDiscountPrice()) / $this->getPrice()) * 100);
    }

    
    /*****************
     * Retrieves the ids of the attribute values associated with the product
     * These ids are used with the product filter
     * Restructures from associative to array to an indexed array for streamlined array searching
     *****************************/
    private function retrieveAttributeValueIds() {
        $source = new ProductCRUD();
        $attributes = $source->getProductAttributeValueIds($this->getId());
        $indexedAttributes = [];
        foreach($attributes as $attribute) {
            array_push($indexedAttributes, $attribute['attribute_value_id']);
        }
      
        $this->setAttributes($indexedAttributes);
    }


    /*************
     * Retrieves product attributes, creates and returns the html
     * Does this on the fly instead of using instance variables
     * as these are only needed on the product page
     *********************/
    private function getProductAttributesFull() {
        $source = new ProductCRUD();
        $html = "<p style='padding:20px;cursor:default;padding-bottom:200px;'><i>Product details currently unavailable</i></p>";
        $attributes = $source->getProductAttributesFull($this->getId());
        
        if ($attributes) {
            $html = "";
            foreach($attributes as $attribute) {
                $html.="<div class='attribute-item'>";
                    $html.="<p>".$attribute['title']."</p>";
                    $html.="<p>".$attribute['value']."</p>";
                $html.="</div>";
            }
        }
        return $html;
    }

    
    /*************
     * Retrieves product overviews, creates and returns the html
     * Does this on the fly instead of using instance variables
     * as these are only needed on the product page
     *********************/
    private function getProductOverviews() {
        $source = new ProductCRUD();
        $html = "<p style='padding:20px;cursor:default;padding-bottom:200px;'><i>Product description currently unavailable</i></p>";
        $overviews = $source->getProductOverviews($this->getId());

        if ($overviews) {
            $html = "";
            foreach($overviews as $overview) {
                $html.="<div class='overview-item'>";
                    if ($overview['image']) {
                        $html.="<img src='".$overview['image']."'>";
                    }
                    $html.="<h3>".$overview['heading']."</h3>";
                    $html.="<p>".$overview['text_body']."</p>";
                $html.="</div>";
            }
        }
        return $html;
    }

    /*******************************
     * Constructs and returns the html for a product search result
     ******************************************/
    public function displayProductAsSearchResult() {
        $html = "";

        // Search result item container
        $html.="<div class='search-result-item'>";
            // product details
            $html.="<img src='".$this->getImage()."'>";
            $html.="<div class='sr-text-container'>";
                $html.="<h4>".$this->getName()."</h4>";
                // Pricing logic
                $html.="<div class='sr-price-container'>";
                    if ($this->isDiscounted()) {
                    $html.="<p class='sr-product-price-discounted'>£".$this->getPrice()."</p>";
                    $html.="<p class='sr-product-price'>£".$this->getDiscountPrice()."</p>";
                    } else {
                    $html.="<p class='sr-product-price'>£".$this->getPrice()."</p>";
                    }
                $html.="</div>";
            $html.="</div>";
            $html.="<a class='sr-wrapper-link' href='/productpage.php?pid=".$this->getId()."'><span></span></a>";
        $html.="</div>";
        return $html;
    }



    /************
     * Constructs and returns the html for the product page
     ****************************/
    public function displayProductPage() {
        $html = "";
        // Top portion of product container
        $html.="<div class='product-top-container'>";
            $html.="<div class='product-top-left'>";
                $html.="<img src='".$this->getImage()."'>";
            $html.="</div>";
            $html.="<div class='product-top-right'>";
                // Name
                $html.="<div class='product-name-container'>";
                    $html.="<h2>".$this->getName()."</h2>";
                    $html.="<span class='product-type'>".$this->getType()."</span>";
                $html.="</div>";
                // Short description / Header
                $html.="<p class='product-desc-short'>".$this->getAlcoholVolume()." abv / ".$this->getBottleSize()."</p>";

                // Price
                if ($this->isDiscounted()) {
                    $html.="<div class='discount-price-container'>";
                        $html.="<h3 class='product-price-discounted'>£".$this->getPrice()."</h3>";
                        $html.="<h3 class='product-price'>£".$this->getDiscountPrice()."</h3>";
                        $html.="<p class='percentage-indicator'>-".$this->getDiscountPercentage()."%</p>";
                    $html.="</div>";
                } else {
                    $html.="<div class='discount-price-container'>";
                        $html.="<h3 class='product-price'>£".$this->getPrice()."</h3>";
                    $html.="</div>";
                }

                // Quantity input and add to cart button
                $html.="<label for='product-quantity'>Quantity: <input type='number' name='product-quantity' value='1'></label>";
                if ($this->getSTock() > 0) {
                    $html.="<input type='hidden' id='product-id' value='".$this->getId()."'>";
                    $html.="<button name='add-to-cart' class='add-to-cart-btn'>Add to cart</button>";
                } else {
                    $html.="<button class='out-of-stock-btn'>Out of stock</button>";
                }

                // Description
                $html.="<p class='product-description'>".$this->getDescription()."</p>";

                

            $html.="</div>";
        $html.="</div>";
        // Bottom portion of product container
        $html.="<div class='product-bottom-container'>";
            // Dummy container to align page consistently
            $html.="<div class='product-bottom-left'></div>";
            // Container for the dynamic product detail tabs
            $html.="<div class='product-bottom-right'>";
                // Buttons to open tabs name attribute contains the key used to open tabs
                $html.="<div class='product-bottom-tab-btns'>";
                    $html.="<h3 name='attributes' class='tab-btn'>Details</h3>"; 
                    $html.="<h3 name='overviews' class='tab-btn'>Description</h3>";
                $html.="</div>";
                // First tab - product attributes
                $html.="<div class='attributes tab' id='attributes'>";
                    $html.=$this->getProductAttributesFull();
                $html.="</div>";
                // Second tab - product overviews
                $html.="<div class='overviews tab' id='overviews'>";
                    $html.=$this->getProductOverviews();
                $html.="</div>";
                // Third tab - reviews?
            $html.="</div>";
        $html.="</div>";

        return $html;
    }

    public function displayProductFeatured() {
        $html = "";
        $html.="<div class='featured-product'>";
            $html.="<img  class='owl-lazy' data-src='".$this->getImage()."' src='".$this->getImage()."' />";
            $html.="<div class='product-name-container'><h3>".$this->getName()."</h3></div>";
            $html.="<h4 class='product-type'>".$this->getType()."</h4>";
            $html.="<p class='product-desc-short'>".$this->getAlcoholVolume()." abv / ".$this->getBottleSize()."</p>";
            $html.="<div class='product-price-container'>";
                if ($this->isDiscounted()) {
                    $html.="<p class='percentage-indicator'>-".$this->getDiscountPercentage()."%</p>";
                    $html.="<p class='product-price-discounted'>£".$this->getPrice()."</p>";
                    $html.="<p class='product-price'>£".$this->getDiscountPrice()."</p>";
                } else {
                    $html.="<p class='product-price'>£".$this->getPrice()."</p>";
                }
                $html.="</div>";
                $html.="<a class='wrapper-link' href='/productpage.php?pid=".$this->getId()."'><span></span></a>";

        $html.="</div>";

        return $html;
    }


    public function adminDisplayProductTableItems() {
        $html = "";

        // Product item row
        $html.="<tr class='product-management-item' id='".$this->getId()."'>";
            // Img, Name and Price
            $html.="<td><div class='td-flex-center'><img style='width:35px;' src='".$this->getImage()."'><h5 id='product-name-".$this->getId()."'>".$this->getName()."</h5><p id='product-base-price-".$this->getId()."' price='".$this->getPrice()."'>(£".$this->getPrice().")</p></div></td>";

            // Active state slider
            $html.="<td><div class='td-flex-center'>";
                $html.="<label class='switch'>";
                if ($this->isActive()) {
                    $html.="<input type='checkbox' id='active-".$this->getId()."' checked>";
                } else {
                    $html.="<input type='checkbox' id='active-".$this->getId()."'>";
                }
                    $html.="<span class='slider'></span>";
                $html.="</label>";
            $html.="</div></td>";

            // Featured state slider
            $html.="<td><div class='td-flex-center'>";
            $html.="<label class='switch'>";
                if ($this->isFeatured()) {
                    $html.="<input type='checkbox' id='featured-".$this->getId()."' checked>";
                } else {
                    $html.="<input type='checkbox' id='featured-".$this->getId()."'>";
                }
                    $html.="<span class='slider'></span>";
            $html.="</label>";
            $html.="</div></td>";

            // Discount management
            $html.="<td id='product-stock-data-".$this->getId()."'><div class='td-flex-center'>";
                if ($this->isDiscounted()) {
                    $html.="<div><label class='container-label'>Discount price: &nbsp;";
                    $html.="<input type='number' step='0.01' id='discount-price-".$this->getId()."' value='".$this->getDiscountPrice()."'>";
                    $html.="</label></div>";
                    $html.="<div><label class='container-label'>End date:  &nbsp;";
                        $html.="<input type='datetime-local' id='discount-end-datetime-".$this->getId()."' value='".date('Y-m-d\TH:i',strtotime($this->getDiscountEndDatetime()))."'>";
                    $html.="</label></div>";
                    $html.="<button id='update-discount-".$this->getId()."'><i class='fas fa-wrench'></i>Save</button>";
                    $html.="<button class='delete-action-btn' id='end-discount-".$this->getId()."'><i class='fas fa-hourglass-end'></i>End</button>";
                    $html.="";
                } else {
                    $html.="<button class='add-action-btn' id='add-discount-".$this->getId()."'>Add discount</button>";
                }
            $html.="</div></td>";

            // Stock management
            $html.="<td style='max-width:100px;'><div class='td-flex-center' style='flex-direction:column;align-items:flex-start;'>";
                $html.="<p>Stock: <span id='current-stock-".$this->getId()."' style='font-weight:500;'>".$this->getStock()."</span></p>";
                $html.="<div><label class='container-label'>Qty to add/remove: &nbsp;";
                    $html.="<input type='number' style='width:90%;' id='product-stock-".$this->getId()."'>";
                $html.="</label></div>";
                $html.="<button class='update-action-btn' style='width:100%;' id='add-stock-".$this->getId()."'><i class='fas fa-plus'></i> Add</button>";
                $html.="<button class='delete-action-btn' style='width:100%;' id='remove-stock-".$this->getId()."'><i class='fas fa-minus'></i> Remove</button>";
            $html.="</div></td>";

            // Options
            $html.="<td><div class='td-flex-center td-flex-justify-between'>";
                $html.="<a class='edit-product-btn' id='edit-product-".$this->getId()."' href='admin-edit-product.php?pid=".$this->getId()."'>Edit <i class='far fa-edit'></a>";
            $html.="</div></td>";
        $html.="</tr>";

        return $html;
    }

    /************
     * __toString used to display products on the category pages
     ***********************************/
    public function __toString() {
        $html = "";
            $html.="<div class='product'>";
                $html.="<img src='".$this->getImage()."' loading='lazy'>";
                $html.="<h3 class='product-name'>".$this->getName()."</h3>";
                $html.="<h4 class='product-type'>".$this->getType()."</h4>";
                $html.="<p class='product-desc-short'>".$this->getAlcoholVolume()." abv / ".$this->getBottleSize()."</p>";
                $html.="<input type='hidden' name='product_id' value='".$this->getId()."'>";
                $html.="<div class='product-price-container'>";
                if ($this->isDiscounted()) {
                    $html.="<p class='percentage-indicator'>-".$this->getDiscountPercentage()."%</p>";
                    $html.="<p class='product-price-discounted'>£".$this->getPrice()."</p>";
                    $html.="<p class='product-price'>£".$this->getDiscountPrice()."</p>";
                } else {
                    $html.="<p class='product-price'>£".$this->getPrice()."</p>";
                }
                $html.="</div>";
                /*
                if ($this->getSTock() > 0) {
                    $html.="<input type='hidden' value='".$this->getId()."'>";
                    $html.="<button name='add-to-cart' type='submit'>Add to cart</button>";
                } else {
                    $html.="<button class='out-of-stock-btn'>Out of stock</button>";
                }
                */
                $html.="<a class='product-wrapper-link' href='/productpage.php?pid=".$this->getId()."' target='_blank'><span></span></a>";
            $html.="</div>";
        return $html;
    }

}

?>