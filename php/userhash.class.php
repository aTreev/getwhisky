<?php

class UserHash {
	private $hashed;
	private $options=['cost' => 10,];
	
	public function __construct() {	}

	private function setHash($hash){$this->hashed=$hash;}
	public function getHash(){return $this->hashed;}

	// store a hash and salt pair passed in to the method
	public function initHash($hash) {
		$this->setHash($hash);
	}


	
	// newHash() takes plaintext password and runs it through the pass hash function
	// takes 3 params

	// $plaintext 			- 	the unencrypted password
	// PASSWORD_DEFAULT 	- 	represents the algorithm to be used (auto adjusts to
	//							the latest version of bcrypt)
	// $this->options 		-	options field contains a cost param, cost refers to the
	//							computational effort required to generate the hash
	public function newHash($plaintext) {
		$this->setHash(password_hash($plaintext,PASSWORD_DEFAULT,$this->options));
	}

	
	// authentication occurs by running a plaintext password through the same hashing algorithm
	// compares the passed password's hash to the user's hash 
	public function testPass($plaintext) {
		return password_verify($plaintext,$this->getHash());
	}

	
	// checkRules() tests the complexity of the password
	// sets a minimum and maximum length 
	// (max length for the algorithm is 72 characters)
	public function checkRules($password) {
		$valid=true;
		if(strlen(trim($password))<8 || strlen(trim($password))>72) {$valid=false;}
		return $valid;
	}

}
/*
	test for the userHash file

	$newhash = new UserHash();
	$newhash->newHash('Pa$$w0rd');
	var_dump($newhash);
	if($newhash->testPass('Pa$$w0rd')) {
		echo "<br />Password ok";
	} else {
		echo "<br />Password not ok";
	}
*/
?>
