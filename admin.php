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
	<header>
		<?php
			echo $page->displayHeader();
		?>
	</header>
	<h1>Admin Page</h1>
	

	<!-- prints the userlist to a form using the editother.php page -->
	<form method="post" action="editother.php">
		<?php
			$userlist = new UserList();
			echo $userlist;
		?>
		<button type="submit">Edit User</button>
	</form>

</body>
</html>
