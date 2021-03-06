<?php 
    require_once("php/page.class.php");
    $page = new Page();
    /****************
     * Product page
     * HTML for this page is constructed through the
     * product.class.php displayProductPage() method
     *******/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        echo $page->displayHead();
    ?>
    <link rel="stylesheet" href="style/css/product-page.css">
    <link rel="stylesheet" href="style/css/index.css">
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
    <?php 
    if (isset($_GET['pid']) && util::valInt($_GET['pid'])) {
        $productId = util::sanInt($_GET['pid']);
        $productFound = $page->setPageProductById($productId);
        if ($productFound) {
        ?>
        <title><?php echo $page->getProduct()->getName(); ?></title>

        <div id='product-root'>
            <?php echo $page->displayProductPage(); ?>
        </div>
        <div id='related-products-root'>
            <?php 
                echo $page->displayRelatedProducts();
            ?>
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
<script src='js/classes/alert.class.js'></script>
<script src="owl-carousel/owl.carousel.min.js"></script>
<script src='js/functions.js'></script>
<script src="js/form-functions.js"></script>
<script src='js/product-page.js'></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareProductPage();
        }
    }
</script>
</html>