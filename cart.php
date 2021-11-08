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
        <div class="cart-heading">
            <h3>Your shopping basket</h3>
        </div>
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
