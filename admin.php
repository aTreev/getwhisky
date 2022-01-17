<?php
    require_once("php/page.class.php");
    $page = new Page(3);
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky Admin page</title>
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
                <h1>Getwhisky Admin Panel</h1>
            </div>
        </div>

        <div class="admin-content-container">

            <!-- Product Management -->
            <div class="admin-card">
                <div class="card-top">
                    <div class="circle"><i class="fas fa-boxes"></i></div>
                </div>
                <div class="card-bot">
                    <h4>Product Management</h4>
                    <p>Manage product stock, create and edit products</p>
                    <span><a class='wrapper-link' href="admin-product-management.php"></a></span>
                </div>
            </div>

            <!-- Product Management -->
            <div class="admin-card">
                <div class="card-top">
                    <div class="circle"><i class="fas fa-wrench"></i></div>
                </div>
                <div class="card-bot">
                    <h4>Category Management</h4>
                    <p>Manage categories, product filters and product attribute options</p>
                    <span><a class='wrapper-link' href="admin-category-management.php"></a></span>
                </div>
            </div>

        </div>

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
