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
    $htmlToReturn = "";
    $filteredTwice = false;
    
    // Category passed filter products by category
    if (isset($_POST['catid']) && util::valInt($_POST['catid'])) {
        $categoryId = util::sanInt($_POST['catid']);
        foreach($allProducts as $product) {
            if ($product->getCategoryId() == $categoryId) {
                array_push($filteredProducts, $product);
                $allProducts = $filteredProducts;
            }
        }


        // Additional filters passed, further filter the products
        if (isset($_POST['attribute_values'])) {
            $filteredTwice = true;
            
            // Sanitize all array inputs
            $attributeValues = array_filter($_POST['attribute_values'], "ctype_digit");
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

        
        foreach($filteredProducts as $product) {
            $htmlToReturn.=$product;
        }

        $result = ["html" => $htmlToReturn];
        // If filters have been applied return the number of products found
        if ($filteredTwice) {
            $result = ["html" => $htmlToReturn, "count" => count($filteredProducts)];
        }
        echo json_encode($result);
        //echo $result['html'];
        //echo $result['count'];
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