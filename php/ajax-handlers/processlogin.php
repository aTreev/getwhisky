
<?php
	require_once("../page.class.php");
	if(util::valEmail($_POST['email']) && util::valStr($_POST['password'])) {
		$checkoutLogin = false;
		if (isset($_POST['checkoutPage']) && ($_POST['checkoutPage'] == 1)) $checkoutLogin = true;
		$email=$_POST['email'];
		$userpass=$_POST['password'];
		$page=new Page();
		
		$result = $page->login($email,$userpass, $checkoutLogin);
		echo json_encode($result);
	} else {
		echo json_encode(['authenticated' => 0]);
	}
	
?>

