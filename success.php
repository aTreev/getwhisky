<?php 
    require_once("php/page.class.php");
    $page = new Page(2);
    if (count($page->getCart()->getItems()) > 0) header("Location: /cart.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead();?>
    <title>Thank you for your order!</title>
    <link rel="stylesheet" href="style/css/cart.css">
</head>
<body>
    <?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
?>
  <main>
  <div class="cart-position-container">
            <div class="content-container">
                <div class="position-item">
                    <a href='#' class='previous-link'>
                        <i class="position-number fas fa-check"></i>
                        <p class="position-text">Basket</p>
                    </a>
                </div>
                <div class="position-item">
                <a href='#' class='previous-link'>
                        <i class="position-number fas fa-check"></i>
                        <p class="position-text">Details</p>
                    </a>
                </div>
                <div class="position-item">
                    <p class="position-number">3</p>
                    <p class="position-text">Delivery</p>
                </div>
                <div class="position-item">
                    <p class="position-number">4</p>
                    <p class="position-text">Payment</p>
                </div>
                <div class="position-item position-active">
                    <p class="position-number">5</p>
                    <p class="position-text">Thanks!</p>
                </div>
            </div>
        </div>
    <section style='width:1400px;margin:auto;margin-top:40px;'>
      <p>
        We appreciate your business! If you have any questions, please email
        <a href="mailto:orders@example.com">orders@example.com</a>.
      </p>
      <?php 
      ?>
    </section>
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