<?php 
/***********
 * File for calling PHP scripts through AJAX on the product management page
 * Functions should be self explanitory as they're fairly simple and mainly
 * calling methods through a page or CRUD object
 ********/
require_once("../page.class.php");

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
            getProductsFromSearch();
        break;
        case 6:
            getBaseProductManagementHtml();
        break;
        case 7:
            updateProductStock();
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
    if ( (util::valInt($_POST['productid'])) && (util::valFloat($_POST['price'])) && util::valStr($_POST['endDatetime']) ) {
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

function getProductsFromSearch() {
    if (util::valStr($_POST['searchString'])) {
        $searchStr = util::sanStr($_POST['searchString']);
        $page = new Page();

        $products = $page->getProducts();
        $returnHtml = "";
        $result = 0;

        foreach($products as $product) {
            if (preg_match("/{$searchStr}/i", $product->getName())) {
                $returnHtml.=$product->adminDisplayProductTableItems();
            }
        }

        if ($returnHtml) $result = 1;

        $result = ['result' => $result, 'html' => $returnHtml];
        echo json_encode($result);
    }
}

function getBaseProductManagementHtml() {
    $page = new Page();
    echo json_encode($page->adminDisplayProductManagementPage());
}

function updateProductStock() {
    if (util::valInt($_POST['productid']) && util::valInt($_POST['quantity'])) {
        $productid = util::sanInt($_POST['productid']);
        $quantity = util::sanInt($_POST['quantity']);
        $productCRUD = new ProductCRUD();

        $result = $productCRUD->updateProductStockB($productid, $quantity);

        echo json_encode($result);
    }
}
?>