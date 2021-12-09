<?php
    //includes to generate an anonymous menu
    require_once("php/page.class.php");
    $page = new Page(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky homepage</title>
    <link rel="stylesheet" href="style/css/index.css">
</head>
<body>
    <?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
    ?>
    <main>
        <div class="hero">
            <img src="/assets/getwhisky-banner.png" alt="" srcset="">
        </div>
    </main>
</body>
<script src="js/functions.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
        }
    }
</script>
</html>