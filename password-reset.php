<?php 
    require_once("php/util.class.php");
    require_once("php/page.class.php");
    $page = new Page();
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        echo $page->displayHead();
    ?>
    <title>password recovery</title>
</head>
<body>
    <header>
        <?php 
            echo $page->displayHeader();
        ?>
    </header>
    <main>
        <?php 
            if (util::valStr($_GET['resetKey'])) {
                $resetKey = util::sanStr($_GET['resetKey']);
                $userCRUD = new UserCRUD();

                // If user cancels the reset request
                if (isset($_GET['cancel']) && util::valBool($_GET['cancel']) && $_GET['cancel'] == 1) {
                    $result = $userCRUD->wipeResetKey($resetKey);
                    echo $result;
                    if ($result) {
                        // Password reset token cancelled display success
                        ?>
                        <div class="verify-container">
                            <h3>The password reset request has been cancelled</h3>
                            <p>Please contact us at info@getwhisky.com if you have any issues logging into your account</p>
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
                        // Reset token already cancelled display error message
                        ?>
                        <div class="verify-container">
                            <h3>The password reset request was already cancelled</h3>
                            <p>Please contact us at info@getwhisky.com if you have any issues logging into your account</p>
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
                    return;
                }

                // Reset initiated
                $found = $userCRUD->checkPasswordResetKey($resetKey);
                if ($found) {
                    ?>
                    <form class="form-main" method="POST" action="process-password-reset.php">
                        <div class='form-header'>
                            <h3>password recovery</h3>
                            <p>To reset your password please enter a new password in the input box below</p>
                        </div> 
                        <div class="input-container-100">
                            <input type="hidden" name="resetKey" value="<?php echo $resetKey;?>">
                            <input type="password" class="form-item" name="userpass" id="password-reset-input" placeholder="enter new password">
                        </div>
                        <button id="reset-submit" type="submit">submit</button>
                    </form>
                    <script src='js/functions.js'></script>
                    <script>
                        let passwordStrength;
                        $("#password-reset-input").keyup(function(){
                            passwordStrength = testPasswordStrength($("#password-reset-input"));
                        });

                        $("#reset-submit").click(function(e){
                            if (passwordStrength > 8) return true;
                            e.preventDefault();
                        });
                    </script>
                    <?php
                } else {
                    ?>
                    <div class="verify-container">
                        <h3>The password reset has already been processed</h3>
                        <p>Please contact us at info@getwhisky.com if you have any issues logging into your account</p>
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
            } else {
                header("Location: /index.php");
            }
        ?>
    </main>
</body>
</html>