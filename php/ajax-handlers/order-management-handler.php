<?php 
require_once("../util.class.php");
require_once("../order.class.php");
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

    switch ($functionToCall) {
        case 1:
            retrieveAllOrders();
        break;

        case 2:
            setOrderStatusToDispatched();
        break;

        case 3:
            cancelOrderAndIssueRefund();
        break;

        case 4:
            orderManuallyRefunded();
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

        $result = $orderCRUD->updateOrderStatus($orderid, "dispatched");
        $result = $orderCRUD->updateAdminOrderStatus($orderid, "dispatched");
        if ($result) {
            // email customer
            $orderObj = new Order($orderCRUD->getOrderFullByOrderid($orderid)[0]);
            $orderHtml = $orderObj->displayOrderAdmin();
        }
        echo json_encode(['result' => $result, 'new_html' => $orderHtml]);
    }
}

function cancelOrderAndIssueRefund() {
    if (util::valStr($_POST['orderid']) && util::valStr($_POST['stripe_payment_intent']) && util::valFloat($_POST['orderTotal'])) {
        require_once('../../stripe/init.php');
        $orderid = util::sanStr($_POST['orderid']);
        $paymentIntent = util::sanStr($_POST['stripe_payment_intent']);
        $amountToRefund = util::sanStr($_POST['amount_to_refund']);
        $orderTotal = util::sanStr($_POST['orderTotal']);
        $orderCRUD = new OrderCRUD();
        $failureReason = "";

        if ($amountToRefund == 0) {
            $amountToRefund = $orderTotal;
        }

        $stripe = new \Stripe\StripeClient(
            'sk_test_51Je0ufArTeMLOzQd1e4BFGLKWFOsabluGgErlDnWkmyea9G2LQQJY6PXusduRSaAXhsz6h27Owwz8n9SehfBY3a90087Gcb2ba'
        );

        $refundResult = $stripe->refunds->create([
            'payment_intent' => $paymentIntent,  
            'reason' => 'requested_by_customer',
            'amount' => util::sanFloat($amountToRefund)
        ]);

        if ($refundResult['status'] == "succeeded") {
            $returnResult = 1;
            // success
            // update statuses to refund type, update order refund_amount
            $refundType = "partial_refund";
            if ($amountToRefund == $orderTotal) $refundType = "refunded";
            $orderCRUD->updateOrderStatus($orderid, $refundType);
            $orderCRUD->updateAdminOrderStatus($orderid, $refundType);
            $orderCRUD->updateOrderRefundAmount($amountToRefund, $orderid);
            $orderObj = new Order($orderCRUD->getOrderFullByOrderid($orderid)[0]);
            $orderHtml = $orderObj->displayOrderAdmin();
            // email user
        }
        if ($refundResult['status'] == "failed") {
            $returnResult = 0;
            $failureReason = $result['failure_reason'];
            // handle failure
                // email getwhisky inbox
                // update admin status to refund failure
            $orderCRUD->updateAdminOrderStatus($orderid, "refund_failure");
            $orderObj = new Order($orderCRUD->getOrderFullByOrderid($orderid)[0]);
            $orderHtml = $orderObj->displayOrderAdmin();

        }
        echo json_encode(['result' => $returnResult, 'new_html' => $orderHtml, 'failure_reason' => $failureReason, 'amount_to_refund' => $amountToRefund]);
    }
}

function orderManuallyRefunded() {
    if (util::valStr($_POST['orderid']) && util::valStr($_POST['orderTotal'])) {
        $orderid = util::sanStr($_POST['orderid']);
        $orderTotal = util::sanStr($_POST['orderTotal']);
        $amountRefunded = util::sanStr($_POST['amountRefunded']);
        $orderCRUD = new OrderCRUD();
        $refundType = "partial_refund";
        $result = 0;
        $message = "";
        $orderHtml = "";
        if (!$amountRefunded) {
            $amountRefunded = $orderTotal;
            $refundType = "refunded";
        }

        $result = $orderCRUD->updateOrderRefundAmount($amountRefunded, $orderid);

        // Refund update check
        if ($result) {
            $result = $orderCRUD->updateOrderStatus($orderid, $refundType);
        } else {
            if (!$message) $message = "Failed to update <b>refund amount</b>, please try again";
        }
        
        // Order_status update check
        if ($result) {
            $result = $orderCRUD->updateAdminOrderStatus($orderid, $refundType);
        } else {
            if (!$message) $message = "Failed to update <b>order status</b>, please try again";
        }

        // Admin_order_status update check
        if ($result) {
            $orderObj = new Order($orderCRUD->getOrderFullByOrderid($orderid)[0]);
            $orderHtml = $orderObj->displayOrderAdmin();
            $message = "Order status set to '".$refundType."'";
        } else {
            if (!$message) $message = "Failed to update <b>admin order status</b>, manual database update required";
        }

        echo json_encode(['result' => $result, 'message' => $message, 'new_order_html' => $orderHtml]);
    }
}
?>