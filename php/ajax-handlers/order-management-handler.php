<?php 
require_once("../util.class.php");
require_once("../order.class.php");
if (isset($_POST['function']) && util::valInt($_POST['function'])) {
    $functionToCall = util::sanInt($_POST['function']);

    switch ($functionToCall) {
        case 1:
            retrieveAllOrders();
        break;

        case 2:
            setOrderStatusToDispatched();
        break;
    }
}



function retrieveAllOrders() {
    $orders = [];
    $html = [];
    $orderCRUD = new OrderCRUD();
    $retrievedOrders = $orderCRUD->getAllOrders();
    
    foreach($retrievedOrders as $order) {
        array_push($orders, new Order($order));
    }
    foreach($orders as $orderObj) {
        array_push($html, $orderObj->displayOrderAdmin());
    }

    echo json_encode(['html_array' => $html]);
}

function setOrderStatusToDispatched() {
    if (util::valStr($_POST['orderid'])) {
        $orderid = util::sanStr($_POST['orderid']);
        $orderCRUD = new OrderCRUD();
        $result = 0;
        $orderHtml = "";

        $result = $orderCRUD->updateOrderStatusToDispatched($orderid);
        if ($result) {
            // email customer
            $orderObj = new Order($orderCRUD->getOrderFullByOrderid($orderid)[0]);
            $orderHtml = $orderObj->displayOrderAdmin();
        }
        echo json_encode(['result' => $result, 'new_html' => $orderHtml]);
    }
}
?>