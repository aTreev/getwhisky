<?php 
    require_once("php/page.class.php");
    $page = new Page();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        echo $page->displayHead();
        // Title logic
        if (isset($_GET['q']) && util::valStr($_GET['q'])) {
            $searchQuery = util::sanStr($_GET['q']);
            echo "<title>Search results for '${searchQuery}'</title>";
        } else {
            header("Location: /index.php");
        }
    ?>
    <link rel="stylesheet" href="style/css/products.css">
</head>
<body>
    <?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
        echo $page->displayCartNotifications();
    ?>
    <main>
        <input type="hidden" id="search-query" value="<?php echo $searchQuery;?>">

        <div id='category-details'>
            <h3>Search results for: '<?php echo $searchQuery?>'</h3>
            <div id='product-count'></div>
        </div>
        <div class='filter-header'>
            <div></div>
            <div class="sort-by-container">
                <h4>Sort by: &nbsp;</h4>
                <button name='sort' sort-option='latest' style='font-weight:700;'>Latest</button>
                <button name='sort' sort-option='price_low'>Price (low)</button>
                <button name='sort' sort-option='price_high'>Price (high)</button>
            </div>
        </div>
        <div class='product-container'>
            <div id='product-root'></div>
        </div>
    </main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="js/product-search-page.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareSearchPage();
        }
    }
</script>
</html>