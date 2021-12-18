<?php 
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
        break;
        case 4:
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

?>