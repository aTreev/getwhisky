<?php
    require_once("php/page.class.php");
    require_once("php/product-category-list.class.php");
    $page = new Page(3);
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky Product Creation Page</title>
    <link rel="stylesheet" href="style/css/product-page.css">
    <link rel="stylesheet" href="style/css/product-creation-page.css">
</head>
<body>
	<?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>
	
	<main>
        <!--
            Change page to emulate a product page
            have text field containers etc for adding details
            Have a blanked out save button until every required field
            has been filled
        -->
        <div id='product-root'>
            <div class="product-top-container">

                <div class="product-top-left">
                    <div class="product-creation-input-container">
                        <label for="product-image">Product Image Upload</label>
                        <div class="input-container-100">
                            <input type="file" id="product-image" id="" class="form-item">
                        </div>
                    </div>
                    <img src="" alt="" id='product-image-preview'>
                </div>

                <div class="product-top-right">
                    <!-- Product Name -->
                    <div class="product-creation-input-container">
                        <label for="product-name">Product Name</label>
                        <div class='input-container-100'>
                            <input type="text" id="product-name" id="" class="form-item">
                        </div>
                        
                    </div>

                    <!-- Product Type -->
                    <div class="product-creation-input-container">
                        <label for="product-type">Product Type</label>
                        <div class='input-container-100'>
                            <input type="text" id="product-type" id="" class="form-item">
                        </div>
                    </div>

                    <!-- Product Price & Stock -->
                    <div class="product-creation-input-container">
                        <label for="product-price">Price</label>
                        <div class='input-container-100'>
                            <input type="number" step="0.01" id="product-price" id="" class="form-item">
                        </div>
                        <label for="product-Stock">Initial Stock</label>
                        <div class='input-container-100'>
                            <input type="number" id="product-stock" id="" class="form-item">
                        </div>
                    </div>

                    <!-- Product Desc -->
                    <div class="product-creation-input-container">
                        <label for="product-desc">Product Description</label>
                        <div class="input-container-100">
                            <textarea id="product-desc" id="" class="form-item"></textarea>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="product-bottom-container">
                <div class="product-bottom-left"></div>

                <div class="product-bottom-right">
                    <div class='category-options-header'>
                        <h3>Category Options</h3>
                    </div>
                    <div>
                        <?php 
                            $categoryList = new ProductCategoryList();
                            echo $categoryList;
                        ?>
                        <div id="product-attribute-selection">
                            <!-- Content added here via JS -->
                        </div>
                    </div>

                    <div class="product-overview-creation">
                        <div class='category-options-header'>
                            <h3>Product Overview Creation</h3>
                        </div>
                        <button>Add Product Overview</button>
                    </div>
                </div>
            </div>
        </div>
        <button id='save-product'>Save</button>


        <div class='modal-container' id='product-success-modal'>
            <div class="modal-header">
                <h3>Product created!</h3>
            </div>
            <div class="modal-content">
                <p>You are now being redirected to the product management page</p>
                <img src="/assets/loader.gif" alt="">
            </div>
        </div>
	</main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/form-functions.js"></script>
<script src="js/create-product-page.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareProductCreationPage();
        }
    }
</script>
</html>
