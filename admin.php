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
        <?php echo $page->adminDisplayAdminPage(); ?>
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
