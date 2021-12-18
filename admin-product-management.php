<?php
    require_once("php/page.class.php");
    $page = new Page(3);
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky product management</title>
    <link rel="stylesheet" href="style/css/admin.css">
</head>
<body>
	<?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>
	<main>
        <h1>Product Management</h1>
        <?php 
            echo $page->adminDisplayProductManagementPage();
        ?>
	</main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/admin-functions.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareProductManagementPage();
        }
    }
</script>
</html>
