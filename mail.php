<?php 
require_once("php/page.class.php");
$page = new Page();
$orderCRUD = new OrderCRUD();
$addressCRUD = new UserAddressCRUD();

$orderDetails = $orderCRUD->getOrderById("9b88eec5bb");
$orderItems = $orderCRUD->getOrderItems("9b88eec5bb");
$addressDetails = $addressCRUD->getUserAddressById($orderDetails[0]['address_id'], $orderDetails[0]['userid']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body style='padding:0;margin:0;'>
    <?php 
        
        echo $html;
    ?>
</body>
</html>