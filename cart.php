<?php
    require_once("php/page.class.php");
    $page = new Page();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky shopping cart</title>
    <link rel="stylesheet" href="style/css/cart.css">
</head>
<body>
	<?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>
	<main>
        <div class="cart-position-container">
            <div class="content-container">
                <div class="position-item position-active">
                    <p class="position-number">1</p>
                    <p class="position-text">Basket</p>
                </div>
                <div class="position-item">
                    <p class="position-number">2</p>
                    <p class="position-text">Details</p>
                </div>
                <div class="position-item">
                    <p class="position-number">3</p>
                    <p class="position-text">Delivery</p>
                </div>
                <div class="position-item">
                    <p class="position-number">4</p>
                    <p class="position-text">Payment</p>
                </div>
                <div class="position-item">
                    <p class="position-number">5</p>
                    <p class="position-text">Thanks!</p>
                </div>
            </div>
        </div>
        <div class="cart-heading"><h2>Your shopping basket</h2></div>
        <?php echo $page->getUser()->getUserid();?>
        <div id="cart-container">
            <?php echo $page->displayCart(); ?>
        </div>
    </main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/cart.js"></script>

<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareCartPage();
        }
    }
</script>
</html>
