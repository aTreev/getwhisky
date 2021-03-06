<?php
    require_once("php/page.class.php");
    $page = new Page();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        echo $page->displayHead(); 
    ?>
    <link rel="stylesheet" href="style/css/verify.css">
</head>
<body>
    <?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
    ?>
    <?php 
        if (isset($_GET['vkey'])) {
            $vKey = $_GET['vkey'];

            $userCRUD = new UserCRUD();
            $result = $userCRUD->verifyUser($vKey);
            if ($result == 1) {
                ?>
                    <div class="verify-container">
                        <h3>Thank you</h3>
                        <p>Your account has been verified</p>
                        <p>You are now being redirected</p>
                        <img src="/assets/loader.gif">
                    </div>
                        
                <?php
            } else {
                ?>
                    <div class="verify-container">
                        <h3>Oops!</h3>
                        <p>Something went wrong</p>
                        <p>This account has already been verified or is invalid</p>
                        <p>You are now being redirected</p>
                        <img src="/assets/loader.gif">
                    </div>
                    <script>
                    </script>  
                <?php
            }
        }
    ?>
</body>
<script src="js/functions.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();

            setTimeout(() => {
                window.location.href = "/user.php";
            }, 5000);
        }
    }
</script>
</html>

