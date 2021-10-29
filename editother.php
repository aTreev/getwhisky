<!-- 
	Page serves the form for the admin to update other users
-->
<?php
	require_once("php/page.class.php");
	require_once("php/user.class.php");
	$page = new Page(3);
	$edituser = new User();
	$editid=$_POST['userid'];
	$found=$edituser->getUserById($editid);
?>

<!doctype html>
<head>
	<?php 
		echo $page->displayHead();
	?>
	<link rel="stylesheet" href="style/css/register.css">
</head>
<body>
	<header>
		<?php 
			echo $page->displayHeader();
		?>
	</header>
	<h1>Update User</h1>

<?php
	if($found) {
?>
	<main>
		<form method="post" action="updateother.php">
			<div class="form_header">
				<h1>Edit Details</h1>
			</div>
			<input type="hidden" class="form_item" name="userid" id="userid" value="<?php echo $edituser->getUserid();?>" required readonly />
			<label for="username">Username</label><input type="text" class="form_item" id="username" name="username" value="<?php echo $edituser->getUsername();?>" required /><br />
			<label for="firstname">First name</label><input type="text" class="form_item" id="firstname" name="firstname" value="<?php echo $edituser->getFirstname();?>" required /><br />
			<label for="surname">Surname</label><input type="text" class="form_item" id="surname" name="surname" value="<?php echo $edituser->getSurname();?>" required /><br />
			<label for="email">Email</label><input type="email" class="form_item" id="email" name="email" value="<?php echo $edituser->getEmail();?>" required /><br />
			<label for="dob">DOB</label><input type="date" class="form_item" id="dob" name="dob" value= "<?php echo $edituser->getDOB();?>" required /><br />
			<label for="usertype">User Type</label>
			<select name="usertype" class="form_item" style="width:50%">
			<option value="1" <?php if($edituser->getUsertype()==1){echo "selected";}?>>Suspended</option>
			<option value="2" <?php if($edituser->getUsertype()==2){echo "selected";}?>>User</option>
			<option value="3" <?php if($edituser->getUsertype()==3){echo "selected";}?>>Admin</option>
			</select><br />
			<label for="userpass">Password</label><input type="password" class="form_item" id="userpass" name="userpass" /><br />
			<div class="submit">
				<button type="submit">Update Details</button>
			</div>
		</form>
		<?php
			} else {
				echo "<p>Cannot find user to edit</p>";
			}
		?>
	</main>	
</body>
</html>
