<?php 
require_once("../page.class.php");
require_once("../util.class.php");

if (isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = $_POST['function'];
    switch ($functionToCall) {
        case 1:
            getFilteredProducts();
        break;
        case 2:
            addToCart();
        break;
    }
}

function getFilteredProducts() {
    $page = new Page();
    // Retrieve all products from page class
    $allProducts = $page->getProducts();
    $filteredProducts = [];
    $filtered = false;
    $htmlToReturn = "";
    
    // Category passed filter products by category
    if (isset($_POST['categoryId']) && util::valInt($_POST['categoryId'])) {
        $categoryId = util::sanInt($_POST['categoryId']);
        foreach($allProducts as $product) {
            if ($product->getCategoryId() == $categoryId) {
                array_push($filteredProducts, $product);
                $allProducts = $filteredProducts;
            }
        }

        // Additional filters passed, further filter the products
        if (isset($_POST['attributeValues'])) {
            $filtered = true;
            
            // Sanitize all array inputs
            $attributeValues = array_filter($_POST['attributeValues'], "ctype_digit");
            // reset filtered products to empty array
            $filteredProducts = [];
            
            // Iterate through all products
            foreach ($allProducts as $product) {
                // Check if all selected filters apply to the product
                $attributeNotInProduct = 0;
                foreach ($attributeValues as $attributeValue) {
                    if (!in_array($attributeValue, $product->getAttributes())) {
                        $attributeNotInProduct++;
                    }
                }
                // If all filters match add the product to 
                if ($attributeNotInProduct == 0) {
                    array_push($filteredProducts, $product);
                }
            }
        }
        
        /*************
         * Sorts the products by the given sorting option
         * PHP object array sort method as seen from
         * https://stackoverflow.com/questions/4282413/sort-array-of-objects-by-object-fields
         * by Roman Yakoviv
         *******/
        if (isset($_POST['sortOption']) && util::valStr($_POST['sortOption'])) {
            $sortOption = util::sanStr($_POST['sortOption']);

            switch($sortOption) {
                case "price_low":
                    // usort with callback function does it magically i guess
                    usort($filteredProducts, function($first, $second){
                        return ($first)->getPrice() > ($second)->getPrice();
                    });
                break;
                case "price_high":
                    usort($filteredProducts, function($first, $second){
                        return ($first)->getPrice() < ($second)->getPrice();
                    });
                break;
                case "latest":
                    // don't sort as already sorted from DB
                break;
            }
        }

        // Set return html
        $products = [];
        for($i = 0; $i < count($filteredProducts); $i++) {
            $products[$i] = $filteredProducts[$i]->__toString();
        }
        $result = ["html" => $products];
        if ($filtered) {
            $result = ["html" =>$products, "count" => count($filteredProducts)];
        }
        echo json_encode($result);
    }
    
}

/*******
 * Adds a product to cart via the page object
 * returns the success state and cartCount
 ******/
function addToCart() {
    if (isset($_POST['productId']) && util::valInt($_POST['productId'])) {
        $productId = $_POST['productId'];
        $page = new Page();

        $result = $page->addToCart($productId);
        $cartCount = $page->getCart()->getCartItemCount();

        $returnResult = ['result' => $result, 'cartCount' => $cartCount];
        echo json_encode($returnResult);
    }
}
?>