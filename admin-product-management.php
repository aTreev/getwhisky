<?php
    require_once("php/page.class.php");
    require_once("php/product-category-list.class.php");
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
        <table id="product-management-table">
            <tr>
                <th>Product</th>
                <th>Active</th>
                <th>Featured</th>
                <th>Discount status</th>
                <th>Stock</th>
                <th>Options</th>
            </tr>
            <tr>
                <td colspan='3'>
                    <div class="td-flex-ccenter" style='width:100%'>
                        <input type='text' placeholder='Product search' id='product-management-search' style='width:95%;padding:15px;'>
                    </div>
                </td>
                <td colspan='3'>
                    <div class='td-flex-center'>
                        <?php 
                            $categoryList = new ProductCategoryList();
                            echo $categoryList->displayListProductManagementPage();
                        ?>
                    </div>
                </td>
            </tr>
            <tbody id="product-management-table-body">
                <!-- Products put here from JS -->
                <tr>
                    <td colspan="100%">
                        <p style='font-style:italic;padding-left:15px;opacity:0.8;'>Use the search bar or select from the category list to retrieve products</p>
                    </td>
                </tr>
            </tbody>
        </table>
        
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
            prepareProductCategorySelect();
        }
    }
</script>
</html>
