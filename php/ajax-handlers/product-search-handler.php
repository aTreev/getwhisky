<?php 
require_once("../page.class.php");
/*************
 * This file is responsible for retrieving products via the search bar and the search results page
 * Uses a location variable to determine whether the request has come from the search bar or the 
 * search results page.
 * Checks for a sortOption POST variable and whether the products should be sorted (only on results page)
 *****************************/
if ((isset($_POST['location']) && util::valStr($_POST['location']))  && (util::posted($_POST['searchQuery']) && util::valStr($_POST['searchQuery']))) {
    // Init variables and retrieve products / post data
    $page = new Page();
    $allProducts = $page->getProducts();
    $searchQuery = util::sanStr($_POST['searchQuery']);
    $location = util::sanStr($_POST['location']);
    $matchingProducts = [];
    $productReturnArray = [];
    $result = ["result" => 0];
    
    // Loop through products adding matching products to the array
    foreach($allProducts as $product) {
        if (preg_match("/{$searchQuery}/i", $product->getName())) {
            array_push($matchingProducts, $product);
        }
    }

    // If any products matched the search query
    if ($matchingProducts) {
        // Sort the products if a sort option was selected
        if (isset($_POST['sortOption']) && util::valStr($_POST['sortOption'])) {
            $matchingProducts = sortSearchResults($matchingProducts, $_POST['sortOption']);
        }
        $countMatching = count($matchingProducts);
        // grab the search result item html for products
        for($i = 0; $i < $countMatching; $i++) {
            // Construct html based on where the request came from
            if ($location == "search-bar") {
                $productReturnArray[$i] = $matchingProducts[$i]->displayProductAsSearchResult();
            }
            if ($location == "search-page") {
            $productReturnArray[$i] = $matchingProducts[$i]->__toString();
            }
        }

        $result = ["html" =>$productReturnArray, "result" => 1];
    }
    echo json_encode($result);
} else {
    echo json_encode($result = ["result" => 0]);
}


function sortSearchResults($products, $sortOption) {
    switch($sortOption) {
        case "price_low":
            // usort with callback function does it magically i guess
            usort($products, function($first, $second){
                return ($first)->getPrice() > ($second)->getPrice();
            });
        break;
        case "price_high":
            usort($products, function($first, $second){
                return ($first)->getPrice() < ($second)->getPrice();
            });
        break;
        case "latest":
            // do nothing, already sorted from db
        break;
        default:
        break;
    }
    return $products;
}


?>