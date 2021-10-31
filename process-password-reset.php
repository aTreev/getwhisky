<?php 
    require_once("php/page.class.php");
    $page = new Page();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <link rel="stylesheet" href="style/css/verify.css">
    <title>password reset result</title>
</head>
<body>
    <header>
        <?php echo $page->displayHeader(); ?>
    </header>
    <main>
        <?php 
            if (util::valStr($_POST['resetKey']) && util::valStr($_POST['userpass'])) {
                $userpass = $_POST['userpass'];
                $resetKey = $_POST['resetKey'];
                $result = $page->resetUserPassword($resetKey, $userpass);
                // If password rseset
                if ($result) {
                    ?>
                    <div class="verify-container">
                        <h3>Your password has been reset</h3>
                        <p>You are now being redirected</p>
                        <p>Please login with your new password</p>
                        <img src="/assets/loader.gif">
                    </div>
                    <script>
                        setTimeout(() => {
                            window.location.href = "/login.php";
                        }, 5000);
                    </script>  
                    <?php
                } else {
                    ?>
                    <div class="verify-container">
                        <h3>Something went wrong</h3>
                        <p>If you are having an issue resetting your password please contact getwhisky at 'email'</p>
                    </div>  
                    <?php
                }
            }
        ?>
    </main>
</body>
</html>