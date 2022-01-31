<?php 
/***********
 * File for calling PHP scripts through AJAX on the product management page
 * Functions should be self explanitory as they're fairly simple and mainly
 * calling methods through a page or CRUD object
 ********/
require_once("../page.class.php");
$page = new Page(3);
if ($page->getUser()->getUsertype() != 3) {
    echo json_encode(0);
    exit();
    die();
    return;
}

if (isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = util::sanInt($_POST['function']);

    switch($functionToCall) {
        case 1:
            toggleProductActiveState();
        break;
        case 2:
            toggleProductFeaturedState();
        break;
        case 3:
            addProductDiscount();
        break;
        case 4:
            endProductDiscount();
        break;
        case 5:
            updateProductStock();
        break;
        case 6:
            getAndDisplayTableProducts();
        break; 
    }
}


// Toggles the active state of the product using the productCRUD PHP class
function toggleProductActiveState() {
    if (isset($_POST['productid']) && util::valInt($_POST['productid'])) {
        $productid = util::sanInt($_POST['productid']);
        $productCRUD = new ProductCRUD();
        $result = $productCRUD->toggleProductActiveState($productid);

        echo json_encode($result);
    }
}

// Toggles the featured state of the product using the productCRUD PHP class
function toggleProductFeaturedState() {
    if (isset($_POST['productid']) && util::valInt($_POST['productid'])) {
        $productid = util::sanInt($_POST['productid']);
        $productCRUD = new ProductCRUD();
        $result = $productCRUD->toggleProductFeaturedState($productid);

        echo json_encode($result);
    }
}

function addProductDiscount() {
    if ((util::valInt($_POST['productid'])) && (util::valFloat($_POST['price'])) && util::valStr($_POST['endDatetime'])) {
        $productCRUD = new ProductCRUD();
        $productid = util::sanInt($_POST['productid']);
        $price = $_POST['price'];
        $endDatetime = util::sanStr($_POST['endDatetime']);

        $result = $productCRUD->createProductDiscount($productid, $price, $endDatetime);
        echo json_encode($result);
        //echo json_encode($price);
    }
}

function endProductDiscount() {
    if (util::valInt($_POST['productid'])) {
        $productCRUD = new ProductCRUD();
        $productid = util::sanInt($_POST['productid']);

        $result = $productCRUD->endProductDiscount($productid);
        echo json_encode($result);
    }
}

function updateProductStock() {
    if (util::valInt($_POST['productid']) && util::valInt($_POST['quantity'])) {
        $productid = util::sanInt($_POST['productid']);
        $quantity = util::sanInt($_POST['quantity']);
        $productCRUD = new ProductCRUD();

        $result = $productCRUD->increaseStockByQuantity($productid, $quantity);

        echo json_encode($result);
    }
}

/********
 * Retrieves the products html for the admin product management page
 * Iterates through products adding their html to the return array.
 * Checks for optional filters and filters the products if neccessary
 * Returns an associative array containing result and html array
 * @returns 
 *      {$result} number: 1 if product html returned 0 if nothing returned
 *      {$productHtmlArray} array: An array of the retrieved products html
 * 
 ********************/
function getAndDisplayTableProducts() {
    $page = new Page();
    $products = $page->getProducts();
    $result = 0;
    $filterByCategory = false;
    $filterByQuery = false;
    
    if (util::valInt($_POST['categoryid'])) {
        $categoryid = util::sanInt($_POST['categoryid']);
        $filterByCategory = true;
    }
    if (util::valStr($_POST['searchString'])) {
        $searchString = util::sanStr($_POST['searchString']);
        $filterByQuery = true;
    }

    if ($filterByCategory) {
        $filteredProducts = [];
        foreach($products as $product) {
            if ($product->getCategoryId() == $categoryid) {
                array_push($filteredProducts, $product);
            }
        }
        $products = $filteredProducts;
    }

    if ($filterByQuery) {
        $filteredProducts = [];
        foreach($products as $product) {
            if (preg_match("/{$searchString}/i", $product->getName())) {
                array_push($filteredProducts, $product);
            }
        }
        $products = $filteredProducts;
    }

    $productHtmlArray = [];
    foreach($products as $product) {
        array_push($productHtmlArray, $product->adminDisplayProductTableItems());
    }
    if (!empty($productHtmlArray)) $result = 1;

    echo json_encode(['result' => $result, 'html' => $productHtmlArray]); 
    
}
?>