<?php
	require_once("php/page.class.php");
	require_once("php/userlist.class.php");
	$page = new Page(3);
?>
<!doctype html>
<head>
<meta charset="UTF-8" />
</head>
<body>

	<h1>Admin Page</h1>
	<nav>
		<ul class="navbar">
			<?php echo $page->getMenu(); ?>
		</ul>
	</nav>

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
