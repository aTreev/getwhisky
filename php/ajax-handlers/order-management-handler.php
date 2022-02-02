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

/************
 * Sets the status of an order to dispatched and emails the customer
 ********************/
function setOrderStatusToDispatched() {
    if (util::valStr($_POST['orderid']) && util::valStr($_POST['userid'])) {
        $orderid = util::sanStr($_POST['orderid']);
        $userid = util::sanStr($_POST['userid']);
        $orderCRUD = new OrderCRUD();
        $userCRUD = new UserCRUD();
        $result = 0;
        $orderHtml = "";

        $result = $orderCRUD->updateOrderStatus($orderid, "dispatched");
        $result = $orderCRUD->updateAdminOrderStatus($orderid, "dispatched");

        if ($result) {
            // email customer
            $email = $userCRUD->getUserEmailByUserid($userid)[0]['email'];
            $emailer = new Emailer($email, constant("noreply_email"), "Your getwhisky order #$orderid has been dispatched");
            $emailer->sendOrderDispatchedEmail($orderid);
            // Fetch new order html
            $orderObj = new Order($orderCRUD->getOrderFullByOrderid($orderid)[0]);
            $orderHtml = $orderObj->displayOrderAdmin();
        }
        echo json_encode(['result' => $result, 'new_html' => $orderHtml]);
    }
}

/******************
 * Function issues a refund/partial-refund through the stripe API
 * if a refund attempt returns the 'succeed' flag then the order's statuses are updated and
 * the customer emailed with the refund amount.
 * 
 * In the case of a failure only the order's admin_status will be updated to reflect a failure
 * Owner then has to issue a refund manually through alternative means.
 **************************************/
function cancelOrderAndIssueRefund() {
    if (util::valStr($_POST['orderid']) && util::valStr($_POST['stripe_payment_intent']) && util::valFloat($_POST['orderTotal']) && util::valStr($_POST['userid'])) {
        require_once('../../stripe/init.php');
        $orderid = util::sanStr($_POST['orderid']);
        $paymentIntent = util::sanStr($_POST['stripe_payment_intent']);
        $amountToRefund = util::sanStr($_POST['amount_to_refund']);
        $orderTotal = util::sanStr($_POST['orderTotal']);
        $userid = util::sanStr($_POST['userid']);
        $orderCRUD = new OrderCRUD();
        $userCRUD = new UserCRUD();
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
            // update statuses to refund type, update order refund_amount
            $refundType = "partial_refund";
            if ($amountToRefund == $orderTotal) $refundType = "refunded";
            $orderCRUD->updateOrderStatus($orderid, $refundType);
            $orderCRUD->updateAdminOrderStatus($orderid, $refundType);
            $orderCRUD->updateOrderRefundAmount($amountToRefund, $orderid);
            // Fetch new order html
            $orderObj = new Order($orderCRUD->getOrderFullByOrderid($orderid)[0]);
            $orderHtml = $orderObj->displayOrderAdmin();
            // email customer
            $email = $userCRUD->getUserEmailByUserid($userid)[0]['email'];
            $emailer = new Emailer($email, constant("noreply_email"), "getwhisky order #$orderid refund");
            $emailer->sendRefundEmail($orderid, $amountToRefund);
        }
        if ($refundResult['status'] == "failed") {
            $returnResult = 0;
            $failureReason = $result['failure_reason'];
            // Update status and fetch new order html
            $orderCRUD->updateAdminOrderStatus($orderid, "refund_failure");
            $orderObj = new Order($orderCRUD->getOrderFullByOrderid($orderid)[0]);
            $orderHtml = $orderObj->displayOrderAdmin();

        }
        echo json_encode(['result' => $returnResult, 'new_html' => $orderHtml, 'failure_reason' => $failureReason, 'amount_to_refund' => $amountToRefund]);
    }
}

/**********
 * Function to manually set the status of an order
 * to refunded.
 * This function is only called should a stripe refund fail
 * and the owner has to manually refund through other means
 * 
 * Updates the order status, emails the customer and returns
 * an updated order obj html
 *************************************/
function orderManuallyRefunded() {
    if (util::valStr($_POST['orderid']) && util::valStr($_POST['orderTotal']) && util::valStr($_POST['userid'])) {
        $orderid = util::sanStr($_POST['orderid']);
        $orderTotal = util::sanStr($_POST['orderTotal']);
        $amountRefunded = util::sanStr($_POST['amountRefunded']);
        $userid = util::sanStr($_POST['userid']);
        $orderCRUD = new OrderCRUD();
        $userCRUD = new UserCRUD();
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
            // entire process successfull
            $orderObj = new Order($orderCRUD->getOrderFullByOrderid($orderid)[0]);
            $orderHtml = $orderObj->displayOrderAdmin();
            $message = "Order status set to '".$refundType."'";
            // email customer
            $email = $userCRUD->getUserEmailByUserid($userid)[0]['email'];
            $emailer = new Emailer($email, constant("noreply_email"), "getwhisky order #$orderid refund");
            $emailer->sendRefundEmail($orderid, $amountRefunded);
        } else {
            if (!$message) $message = "Failed to update <b>admin order status</b>, manual database update required";
        }

        echo json_encode(['result' => $result, 'message' => $message, 'new_order_html' => $orderHtml]);
    }
}
?>