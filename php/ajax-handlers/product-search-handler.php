<?php 
require_once("../page.class.php");


if (util::posted($_POST['searchQuery']) && util::valStr($_POST['searchQuery'])) {
    $page = new Page();
    $allProducts = $page->getProducts();
    $searchQuery = util::sanStr($_POST['searchQuery']);
    $matchingProducts = [];
    $productReturnArray = [];
    
    foreach($allProducts as $product) {
        if (preg_match("/{$searchQuery}/i", $product->getName())) {
            array_push($matchingProducts, $product);
        }
    }

    if ($matchingProducts) {
        $countMatching = count($matchingProducts);

        for($i = 0; $i < $countMatching; $i++) {
            $productReturnArray[$i] = $matchingProducts[$i]->displayProductAsSearchResult();
        }
        
        $result = ["html" =>$productReturnArray, "result" => 1];
    } else {
        $result = ["result" => 0];
    }
    
    echo json_encode($result);
}

?>