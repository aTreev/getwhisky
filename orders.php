<?php 
    require_once("php/page.class.php");
    $page = new Page(1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        echo $page->displayHead();
    ?>
    <link rel="stylesheet" href="style/css/user-order-page.css">
    <title>Order page</title>
</head>
<body>
    <?php 
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>
    <main>
        <div id="order-root">
            <div class="order-root-header">
                <h2>My orders</h2>
            </div>
            <?php 
                echo $page->getUser()->getAndDisplayOrderPage();
            ?>

        </div>
    </main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
        }
    }
</script>
</html>