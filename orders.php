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
    <link rel="stylesheet" href="style/css/user.css">
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
        <div class="account-header">
            <h2>My Orders</h2>
            <a href="/user.php">back to user profile</a>
        </div>
        <div id="user-root">
            <?php echo $page->getUser()->displayAccountOptionsSidebar(); ?>
            <div class="account-main-content">
                <div id="order-root">
                    <div class="form-header">
                        <h3>View your previous orders</h3>
                    </div>
                    <?php 
                        echo $page->getUser()->getAndDisplayOrderPage();
                    ?>
                </div>
            </div>
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