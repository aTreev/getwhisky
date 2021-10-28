<?php
    // Page provides users with a form to update their details
    require_once("php/page.class.php");
    $page = new Page(2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/register.css">
    <title>Document</title>
</head>
<body>
    <nav>
        <ul class="navbar">
            <?php echo $page->getMenu(); ?>
        </ul>
    </nav>

    
    <form method="post" action="updateself.php">
        <div class="form_header">
            <h1>Edit Details</h1>
        </div>
        <input type="hidden" class="form_item" name="userid" id="userid" value="<?php echo $page->getUser()->getUserid();?>" required readonly />
        <label for="username">Username</label><input type="text" class="form_item" id="username" name="username" value="<?php echo $page->getUser()->getUsername();?>" required /><br />
        <label for="firstname">First name</label><input type="text" class="form_item" id="firstname" name="firstname" value="<?php echo $page->getUser()->getFirstname();?>" required /><br />
        <label for="surname">Surname</label><input type="text" class="form_item" id="surname" name="surname" value="<?php echo $page->getUser()->getSurname();?>" required /><br />
        <label for="email">Email</label><input type="email" class="form_item" id="email" name="email" value="<?php echo $page->getUser()->getEmail();?>" required /><br />
        <label for="dob">DOB</label><input type="date" class="form_item" id="dob" name="dob" value= "<?php echo $page->getUser()->getDOB();?>" required /><br />
        <label for="userpass">Password</label><input type="password" class="form_item" id="userpass" name="userpass" /><br />
        <div class="submit">
            <button type="submit">Update Details</button>
        </div>
    </form>

</body>
</html>