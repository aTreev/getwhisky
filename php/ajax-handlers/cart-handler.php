<?php 
require_once("../page.class.php");

if (isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = $_POST['function'];
    switch ($functionToCall) {
        case 1:
            updateCartItemQuantity();
        break;
        case 2:
            removeFromCart();
        break;
    }
}


function updateCartItemQuantity() {
    if( isset($_POST['productId']) && util::valInt($_POST['productId']) && isset($_POST['quantity']) && util::valInt($_POST['quantity']) ) {
        $page = new Page();
        $productId = util::sanInt($_POST['productId']);
        $quantity = util::sanInt($_POST['quantity']);
        $newCartHtml = "";
        $newCartCount = 0;

        $result = $page->updateCartItemQuantity($productId, $quantity);

        if ($result == 1) {
            $newCartHtml.= $page->displayCart();
            $newCartCount = $page->getCart()->getCartItemCount();
        }

        $returnResult = ['result' => $result, 'html' => $newCartHtml, 'cartCount' => $newCartCount];
        echo json_encode($returnResult);
    } else {
        echo json_encode($result = ['result' => 0]);
    }
}

function removeFromCart() {
    if(isset($_POST['productId']) && util::valInt($_POST['productId'])) {
        $page = new Page();
        $productId = util::sanInt($_POST['productId']);
        $newCartHtml = "";
        $newCartCount = 0;

        $result = $page->removeFromCart($productId);

        if ($result == 1) {
            $newCartHtml.=$page->displayCart();
            $newCartCount = $page->getCart()->getCartItemCount();
        }

        $returnResult = ['result' => $result, 'html' => $newCartHtml, 'cartCount' => $newCartCount];
        echo json_encode($returnResult);    
    } else {
        echo json_encode($result = ['result' => 0]);
    }
}
?>