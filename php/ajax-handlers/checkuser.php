<?php
	// identifies whether or not the username or email have previously been
	// used and returns the result
	require_once("../usercrud.class.php");
	require_once("../util.class.php");

	$email=$_POST['email'];

	$emailexists=util::valEmail($email)?0:2;
	if(util::valEmail($email)) {
		$source=new UserCrud();
		$recordset=$source->testUserEmail($email);
		foreach($recordset as $record) {
			if(strcasecmp($record['email'],$email) == 0) { $emailexists=1;}
		}
	}
	$json=array (
		'emailexists' => $emailexists
		);
	echo json_encode($json);
?>
