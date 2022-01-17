<?php
    require_once("php/page.class.php");
    require_once("php/product-category-list.class.php");
    $page = new Page(3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky Category Management Page</title>
    <link rel="stylesheet" href="style/css/admin.css">
    <link rel="stylesheet" href="style/css/category-management-page.css">
</head>
<body>
    <?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>

    <main>
        <div class='admin-page-header'>
            <div class="admin-header-text-container">
                <h1>Getwhisky Category Management</h1>
                <p><a href="/admin.php">Back to Admin Page</a></p>
            </div>
        </div>

        <div class='category-management-content-container'>
            <h3 style='font-style:italic;opacity:0.8;margin-bottom:20px;'>Select a category to begin managing its filters</h3>
            <?php 
                $categoryList = new ProductCategoryList();
                echo $categoryList
            ?>
            <div class="category-attributes">

            </div>
        </div>
        
    </main> 

</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/category-management-page.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareCategoryManagementPage();
        }
    }
</script>
</html>