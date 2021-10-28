<!--
	Page processes the admin's updateother page
-->
<?php
	require_once("php/page.class.php");
	require_once("php/util.class.php");
	$page = new Page(3);
	try {
		if(util::valInt($_POST['userid'])) {$userid=$_POST['userid'];}
		else { $page->logout();}
		
		$usertype=(util::posted($_POST['usertype'])?$_POST['usertype']:"");
		$username=(util::posted($_POST['username'])?$_POST['username']:"");
		$firstname=(util::posted($_POST['firstname'])?$_POST['firstname']:"");
		$surname=(util::posted($_POST['surname'])?$_POST['surname']:"");
		$email=(util::posted($_POST['email'])?$_POST['email']:"");
		$dob=(util::posted($_POST['dob'])?$_POST['dob']:"");
		$userpass=(util::posted($_POST['userpass'])?$_POST['userpass']:"");
		$result=$page->updateUser($username,$firstname,$surname,$userpass,$email,$dob,$userid, $usertype);
		if($result['update']==1) {
			echo "User updated<br />";
		} else {
			echo "Update Failed:<br>";
			echo $result['messages'];
		}
?>
	<p><a href="admin.php">Back to Admin page</a></p>
<?php
	} catch (Exception $e) {
		echo "Error : ", $e->getMessage();
	}
?>
