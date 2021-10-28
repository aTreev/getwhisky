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
</head>
<body>
    <header>
        <?php echo $page->displayHeader(); ?>
    </header>
    <main>
    </main>
</body>
</html>