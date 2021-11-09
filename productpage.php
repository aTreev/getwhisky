<?php 
    require_once("php/page.class.php");
    $page = new Page();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        echo $page->displayHead();
    ?>
    <link rel="stylesheet" href="style/css/product-page.css">
</head>
<body>
    <?php 
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>
<main>
    <?php 
    if (isset($_GET['pid']) && util::valInt($_GET['pid'])) {
        $productId = util::sanInt($_GET['pid']);
        $productFound = $page->retrieveProductPageProduct($productId);
        if ($productFound) {
        ?>
        <title><?php echo $page->getProduct()->getName(); ?></title>

        <div id='product-root'>
            <?php echo $page->displayProductPage(); ?>
        </div>
        <?php
        } else {
            ?>
            <title>We couldn't find that product</title>
            <?php
        }
    }
    ?>
</main>
</body>
<script src='js/functions.js'></script>
<script src='js/classes/alert.class.js'></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
        }
    }
</script>
</html>