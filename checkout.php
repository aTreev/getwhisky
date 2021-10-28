<?php 
  require_once("php/page.class.php");
  $page = new Page();
?>
<!DOCTYPE html>
<html>
  <head>
    <?php 
      echo $page->displayHead();
    ?>
    <title>getwhisky checkout</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
  </head>
  <body>
    <header>
      <?php
        echo $page->displayHeader();
      ?>
    </header>
    <section>
      <div class="product">
        <img
          src="/assets/lagavulin-16-year-old-whisky.jpg"
          alt="Bottle of Lagavulin Whisky"
          style="width:100px;"
        />
        <div class="description">
          <h3>Lagavulin 16 year</h3>
          <h5>£64.99</h5>
        </div>
      </div>
      <form action="php/create-checkout-session.php" method="POST">
        <button type="submit" id="checkout-button">Checkout</button>
      </form>
    </section>
  </body>
</html>