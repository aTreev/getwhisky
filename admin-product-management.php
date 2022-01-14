<?php
    require_once("php/page.class.php");
    $page = new Page(3);
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky product management</title>
    <link rel="stylesheet" href="style/css/product-management-page.css">
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
                <h1>Getwhisky Product Management</h1>
                <p><a href='/admin.php'>Back to admin page</a></p>
            </div>
        </div>
        <div class='admin-header-link'>
            <div class="admin-header-link-text">
                <a href='admin-create-product.php'>Create new product</a>
            </div>
        </div>
        <?php 
            echo $page->adminDisplayProductManagementTable();
        ?>
	</main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/product-management-page.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareProductManagementPage();
            prepareProductManagementSearch();
        }
    }
</script>
</html>
