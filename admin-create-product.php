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
    <link rel="stylesheet" href="style/css/admin.css">
    <!-- Selectize -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
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

        <div class='admin-page-header'>
            <div class="admin-header-text-container">
                <h1>Getwhisky Product Creation</h1>
                <p><a href="/admin-product-management.php">Back to Product Management</a></p>
            </div>
        </div>
        <div id='product-root'>
            <div class="product-top-container">

                <div class="product-top-left">
                    <div class="product-creation-input-container">
                        <label for="product-image">Product Image Upload</label>
                        <div class="input-container-100">
                            <input type="file" id="product-image" id="" class="form-item" autocomplete="off">
                        </div>
                    </div>
                    <img src="" alt="" id='product-image-preview'>
                </div>

                <div class="product-top-right">
                    <!-- Product Name -->
                    <div class="product-creation-input-container">
                        <label for="product-name">Product Name</label>
                        <div class='input-container-100'>
                            <input type="text" id="product-name" id="" class="form-item" autocomplete="off">
                        </div>
                        
                    </div>

                    <!-- Product Type -->
                    <div class="product-creation-input-container">
                        <label for="product-type">Product Type</label>
                        <div class='input-container-100'>
                            <input type="text" id="product-type" id="" class="form-item" autocomplete="off">
                        </div>
                    </div>

                    <!-- Product Price & Stock -->
                    <div class="product-creation-input-container">
                        <label for="product-price">Price</label>
                        <div class='input-container-100'>
                            <input type="number" step="0.01" id="product-price" id="" class="form-item" autocomplete="off">
                        </div>
                        <label for="product-Stock">Initial Stock</label>
                        <div class='input-container-100'>
                            <input type="number" id="product-stock" id="" class="form-item" autocomplete="off">
                        </div>
                    </div>

                    <!-- Product Desc -->
                    <div class="product-creation-input-container">
                        <label for="product-desc">Product Description</label>
                        <div class="input-container-100">
                            <textarea id="product-desc" id="" class="form-item"></textarea>
                        </div>
                    </div>

                    <!-- Optional values -->
                    <div class="product-creation-input-container bg-white" style='margin-top:40px;border:1px solid lightgrey;border-radius:3px;'>
                        <div style='padding: 20px;'>
                            <div class="section-header">
                                <h3>Additional Details<span style='font-style:italic;font-size:1.4rem;font-weight:400;opacity:0.8;'> (Fill if applicable)</span></h3>
                            </div>
                            
                            <label for="product-alc-volume">Alchohol Volume <i style='cursor:default;font-size:1.4rem;'>(Example: 40%)</i></label>
                            <div class='input-container-100'>
                                <input type="text" id="product-alc-volume" id="" class="form-item" autocomplete="off">
                            </div>
                            <label for="product-bottle-size">Bottle Size <i style='cursor:default;font-size:1.4rem;'>(Example: 70cl / 2 x 20cl)</i></label>
                            <div class='input-container-100'>
                                <input type="text" id="product-bottle-size" id="" class="form-item" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-bottom-container">
                <div class="product-bottom-left"></div>

                <div class="product-bottom-right">
                    
                    <div class='product-category-options-container'>
                        <div class='section-header'>
                            <h3>Category Specific Details</h3>
                        </div>
                        <?php 
                            $categoryList = new ProductCategoryList();
                            echo $categoryList;
                        ?>
                        <div id="product-attribute-selection">
                            <!-- Content added here via JS -->
                        </div>
                    </div>

                    <div class="product-overview-creation-container">
                        <div class='section-header'>
                            <h3>Product Overviews <span style='font-style:italic;font-size:1.4rem;font-weight:400;opacity:0.8;'> Extra Details & Info</span></h3>
                        </div>
                        <input type="hidden" id='count-overview' value='0'>
                        <button id='create-overview'>Add Product Overview</button>
                    </div>

                    <button id='save-product'>Upload Product</button>
                </div>

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
