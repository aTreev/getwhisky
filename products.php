<?php 
    require_once("php/page.class.php");
    $page = new Page();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead();?>
    <title>getwhisky products</title>
    <link rel="stylesheet" href="style/css/products.css">
</head>
<body>
    <?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
    ?>
    <main>
        <div id="products-container">
            <?php
                if (isset($_GET['catid']) && util::valInt($_GET['catid'])) {
                    $categoryId = util::sanInt($_GET['catid']);
                    ?>
                    <input type="hidden" id="category_id" value="<?php echo $categoryId;?>">
                    <div id="filter-root">
                        <div class="filter-header">
                            <h3>Filters</h3>
                        </div>
                        <div class="filter-items">
                            <?php 
                                $page->getCategoryFiltersFromDatabase($categoryId); 
                                echo $page->getCategoryFilters();
                            ?>
                        </div>
                    </div>
                    <div id="product-root"></div>
                    <?php
                } else {
                    // Invalid category provided
                    echo "<div class='no-products-found'><h2>We couldn't find any products!</h2><p>Please try using one of the links in the product menu</p></div>";
                }
            ?>
        </div>
        
    </main>
</body>
<script src="js/products.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareProductsPage();
        }
    }
</script>
</html>