<?php
	// identifies whether or not the username or email have previously been
	// used and returns the result
	require_once("../usercrud.class.php");
	require_once("../util.class.php");

	$username=$_POST["username"];
	$email=$_POST['email'];

	$userexists=util::valUName($username)?0:2;
	$emailexists=util::valEmail($email)?0:2;
	if(util::valEmail($email) && util::valUName($username)) {
		$source=new UserCrud();
		$recordset=$source->testUserEmail($username,$email);
		foreach($recordset as $record) {
			if(strcasecmp($record['username'],$username)==0) { $userexists=1;}
			if(strcasecmp($record['email'],$email)==0) { $emailexists=1;}
		}
	}
	$json=array (
		'userexists' => $userexists,
		'emailexists' => $emailexists
		);
	echo json_encode($json);
?>
