<?php
    require_once("php/page.class.php");
    $page = new Page();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky shopping cart</title>
    <link rel="stylesheet" href="style/css/index.css">
    <link rel="stylesheet" href="style/css/cart.css">
    <link rel="stylesheet" href="owl-carousel/owl.carousel.min.css">
    <link rel="stylesheet" href="owl-carousel/owl.theme.default.min.css">
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

        <?php 
            $last_viewed_categoryid = null;
            if (isset($_SESSION['last_viewed_category']) && util::valInt($_SESSION['last_viewed_category'])) $last_viewed_categoryid = util::sanInt($_SESSION['last_viewed_category']);
            echo $page->getCart()->displayCart($last_viewed_categoryid); 

            if ($page->getCart()->getCartItemCount() == 0) {
                echo $page->displayFeaturedProductsOwl("Why not try some of the getwhisky favourites?");
            }
        ?>
    </main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/functions.js"></script>
<script src="js/cart.js"></script>
<script src="owl-carousel/owl.carousel.min.js"></script>

<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareCartPage();
        }
    }
</script>
</html>
