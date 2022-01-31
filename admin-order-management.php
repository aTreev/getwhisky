<?php
    require_once("php/page.class.php");
    $page = new Page(3);
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky Order Management</title>
    <link rel="stylesheet" href="style/css/admin.css">
    <link rel="stylesheet" href="style/css/order-management-page.css">

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
                <h1>Getwhisky Order Management</h1>
                <p><a href="/admin.php">Back to Admin Page</a></p>
            </div>
        </div>

        <div class="table-wrapper">
            <div class="order-search-container">
                <div>
                    <label for="hide-completed-orders">Hide completed orders?</label>
                    <input type="checkbox" id="hide-completed-orders">
                </div>
                <div>
                    <label for="order-search">Search: </label>
                    <input type='text' id='order-search'> 
                </div>
            </div>
            
            <table id='order-management-table'>
                <thead>
                    <tr>
                        <th style='width: 200px;'>Customer</th>
                        <th style='width: 150px;'>Order number</th>
                        <th style='width: 300px;'>Payment Reference</th>
                        <th style='width: 100px;'>Date</th>
                        <th style='width: 100px;'>Total</th>
                        <th style='width: 100px;'>Delivery Type</th>
                        <th style='width: 150px;'>Status</th>
                        <th style='width: 100px;'>Actions</th>
                    </tr>
                </thead>
                <tbody id='admin-order-root'>
                    
                </tbody>
            </table>
        </div>
        
        
        
    </main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/admin-order-page.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareAdminOrderPage();
        }
    }
</script>
</html>
