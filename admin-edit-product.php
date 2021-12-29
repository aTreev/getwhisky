<?php
require_once("php/page.class.php");
$page = new Page(3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        echo $page->displayHead();
        $productCRUD = new ProductCRUD();
        $product = $productCRUD->getProductById($_GET['pid'])[0];
    ?>
    <title>Editing product - <?php echo $product['name'];?></title>
</head>
<body>
    <?php 
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>

<main>
    <form class='form-inline'>
        <h3>Editing Product <?php echo $product['name']; ?></h3>
    <div class="input-container-100">
        <label for="product-name">Product name</label>
        <input type="text" name='product-name' class="form-item" value='<?php echo $product['name'];?>'>
    </div>
    <div class="input-container-100">
        <label for="product-name">Product name</label>
        <input type="text" name='product-name' class="form-item" value='<?php echo $product['name'];?>'>
    </div>
    </form>
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