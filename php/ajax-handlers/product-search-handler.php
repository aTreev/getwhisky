<?php 
require_once("../page.class.php");


if (util::posted($_POST['searchQuery']) && util::valStr($_POST['searchQuery'])) {
    // Init variables and retrieve products / post data
    $page = new Page();
    $allProducts = $page->getProducts();
    $searchQuery = util::sanStr($_POST['searchQuery']);
    $matchingProducts = [];
    $productReturnArray = [];
    
    // Loop through products adding matching products to the array
    foreach($allProducts as $product) {
        if (preg_match("/{$searchQuery}/i", $product->getName())) {
            array_push($matchingProducts, $product);
        }
    }

    // If any products matched the search query
    if ($matchingProducts) {
        $countMatching = count($matchingProducts);
        // grab the search result item html for products
        for($i = 0; $i < $countMatching; $i++) {
            $productReturnArray[$i] = $matchingProducts[$i]->displayProductAsSearchResult();
        }

        $result = ["html" =>$productReturnArray, "result" => 1];
    } else {
        // No products found return result 0
        $result = ["result" => 0];
    }
    
    echo json_encode($result);
}

?>