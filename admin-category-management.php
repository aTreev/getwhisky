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

        <h3>Select a category to begin editing</h3>
        <?php 
            $categoryList = new ProductCategoryList();
            echo $categoryList
        ?>
    </main> 

</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
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