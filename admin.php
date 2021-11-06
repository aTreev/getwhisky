<?php
    //includes to generate an anonymous menu
    require_once("php/page.class.php");
	require_once("php/userlist.class.php");
    $page = new Page(3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky homepage</title>
</head>
<body>
	<?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
    ?>
	<h1>Admin Page</h1>
	<main>

	<!-- prints the userlist to a form using the editother.php page -->
	<form method="post" action="editother.php">
		<?php
			$userlist = new UserList();
			echo $userlist;
		?>
		<button type="submit">Edit User</button>
	</form>
	</main>
</body>
<script src="js/functions.js"></script>
<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareProductsPage();
        }
    }
</script>
</html>
