<?php
    require_once("php/page.class.php");
    $page = new Page(0);
?>
<!doctype html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <link rel='stylesheet' href='style/css/register.css'>
    <title>getwhisky login</title>
</head>
<body>

    <header>
        <?php echo $page->displayHeader(); ?>   
    </header>
    <main>
        <form method="post" action="processlogin.php">
            <div class="form-header">
                <h3>getwhisky sign in</h3>
            </div>
            <div class="input-container">
            <input class="form-item" type="text" name="username" id="username" placeholder="username"/>
            </div>
            <div class="input-container">
            <input class="form-item" type="password" name="userpass" id="userpass" placeholder="password"/>

            </div>
            <p class='form-link'><a href='/password-reset.php'>Forgotten password?</a></p>
            <p class='form-link'><a href='/register.php'>New to getwhisky? sign up here</a></p>

            <div class="submit">
                <button type="submit">Login</button>
            </div>
        </form>
    </main>
</body>
</html>
