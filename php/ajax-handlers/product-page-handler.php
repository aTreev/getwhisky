<?php 
require_once("../page.class.php");
if(isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = util::sanInt($_POST['function']);

    switch($functionToCall) {
        case 1:
            addToCart();
        break;
    }
}


function addToCart() {
    if( (isset($_POST['productId']) && util::valInt($_POST['productId'])) && (isset($_POST['quantity']) && util::valInt($_POST['quantity']))) {
        $productId = util::sanInt($_POST['productId']);
        $quantity = util::sanInt($_POST['quantity']);

        if ($quantity <= 0) {
            echo json_encode(['result' => 0]);
            return;
        }
        
        $page = new Page();

        $result = $page->addToCart($productId, $quantity);
        $cartCount = $page->getCart()->getCartItemCount();

        $returnResult = ['result' => $result, 'cartCount' => $cartCount];
        echo json_encode($returnResult);

    } else {
        echo json_encode($result = ['result' => 0]);
    }
}
?>