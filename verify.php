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
    <header>
        <?php
            echo $page->displayHeader();
        ?>
    </header>
    <?php 
        if (isset($_GET['vkey'])) {
            $vKey = $_GET['vkey'];

            $userCRUD = new UserCRUD();
            $result = $userCRUD->verifyUser($vKey);
            if ($result == 1) {
                ?>
                    <div class="verify-container">
                        <h1>Thank you</h1>
                        <p>Your account has been verified</p>
                        <p>You are now being redirected</p>
                        <img src="/assets/loader.gif">
                    </div>
                    <script>
                        setTimeout(() => {
                            window.location.href = "/user.php";
                        }, 5000);
                    </script>  
                            
                <?php
            } else {
                ?>
                    <div class="verify-container">
                        <h1>Oops!</h1>
                        <p>Something went wrong</p>
                        <p>This account has already been verified or is invalid</p>
                        <p>You are now being redirected</p>
                        <img src="/assets/loader.gif">
                    </div>
                    <script>
                        setTimeout(() => {
                            window.location.href = "/user.php";
                        }, 5000);
                    </script>  
                <?php
            }
        }
    ?>
</body>
</html>

