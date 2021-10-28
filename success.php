<?php
  require_once("php/page.class.php");
  $page = new Page();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php echo $page->displayHead(); ?>
  <title>Thanks for your order!</title>
</head>
<body>
  <header>
    <?php 
      echo $page->displayHeader();
    ?>
  </header>
  <section>
    <p>
      We appreciate your business! If you have any questions, please email
      <a href="mailto:orders@example.com">orders@example.com</a>.
    </p>
  </section>
</body>
</html>