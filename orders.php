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