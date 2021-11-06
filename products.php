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
        
            <?php
                if (isset($_GET['catid']) && util::valInt($_GET['catid'])) {
                    $categoryId = util::sanInt($_GET['catid']);
                    ?>
                    <div id='category-details'>
                        <?php
                            echo $page->displayCategoryDetails($categoryId);
                        ?>
                        <div id='product-count'></div>

                    </div>
                    <div id="products-container">
                        <input type="hidden" id="category_id" value="<?php echo $categoryId;?>">
                        <div id="filter-root">
                            <div class="filter-header">
                                <h3>Filters</h3>
                                <i class='fas fa-plus'></i>
                            </div>
                            <div class="filter-items">
                                <?php 
                                    echo $page->getCategoryFilters($categoryId); 
                                ?>
                            </div>
                        </div>
                        <div id="product-root"></div>
                    </div>
                    <?php
                } else {
                    // Invalid category provided
                    echo "<div class='no-products-found'><h2>We couldn't find any products!</h2><p>Please try using one of the links in the product menu</p></div>";
                }
            ?>
        
        
    </main>
</body>
<script src="js/functions.js"></script>
<script src="js/products.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareProductsPage();
        }
    }
</script>
</html>