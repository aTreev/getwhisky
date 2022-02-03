<?php
//The UserCRUD class, responsible for storing the data in a persistent data store, attempts to store the new data.
require_once("db.php");
class UserCRUD {
	private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}

	public function getUserByEmail($email, $style=MYSQLI_ASSOC) {
		$this->sql="SELECT * FROM usertable WHERE email = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$email);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function getUserById($userid, $style=MYSQLI_ASSOC) {
		$this->sql="SELECT * FROM usertable WHERE userid = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$userid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function storeSession($id, $session) {
		$this->sql="UPDATE usertable SET lastsession=? WHERE userid=?;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ss",$session,$id);
		$this->stmt->execute();
		return $this->stmt->affected_rows;
	}

	// stores the details passed from the registration()
	// returns error to message if email already exists
	public function storeNewUser($userid,$firstname,$surname,$hash,$email, $vKey) {
		$this->sql="INSERT INTO usertable (userid,firstname,surname,userpass,email, vkey) VALUES(?,?,?,?,?,?);";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ssssss",$userid, $firstname,$surname,$hash,$email, $vKey);
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			$errors="";
			if(strpos($this->stmt->error,'email')) {
				$errors.="Email address exists<br />";
			}
			return $errors;
		} else {
			return $this->stmt->affected_rows;
		}
	}

	// updates a user's details
	public function updateUser($firstname,$surname,$hash,$email,$usertype, $userid) {
		$this->sql="UPDATE usertable SET firstname=?, surname=?, userpass=?, email=?, usertype=? WHERE userid=?;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ssssis",$firstname,$surname,$hash,$email,$usertype,$userid);	
		try {
			$this->stmt->execute();
				return $this->stmt->affected_rows;
		}	catch(Exception $e) {
			if($this->stmt->affected_rows!=1) {
				$errors=['email' => ""];
				if(strpos($this->stmt->error,'email')) {
					$errors['email'] = "Email address already exists";
				}
				return $errors;
			}
		}
	}

	public function testUserEmail($email, $style=MYSQLI_ASSOC) {
		$this->sql="SELECT * FROM usertable WHERE email = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s", $email);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function verifyUser($vKey) {
		$this->sql="UPDATE usertable SET verified = 1 WHERE verified = 0 AND vkey = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$vKey);		
		$this->stmt->execute();

		if($this->stmt->affected_rows!=1) {
			return "There was a problem verifying your account";
		} else {
			return $this->stmt->affected_rows;
		}
	}
	/*******************
	 * Retrieves all userids from the database and returns as
	 * an associative array
	 ******************************************************/
	public function getUserIds($style=MYSQLI_ASSOC) {
		$this->sql="SELECT userid FROM usertable;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function getExistingVerificationKeys($style=MYSQLI_ASSOC) {
		$this->sql="SELECT vkey FROM usertable";
		$this->stmt=self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function getExistingPasswordResetKeys($style=MYSQLI_ASSOC) {
		$this->sql="SELECT password_reset_key FROM usertable";
		$this->stmt=self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function checkPasswordResetKey($resetKey, $style=MYSQLI_ASSOC) {
		$this->sql="SELECT * FROM usertable WHERE password_reset_key = ?";
		$this->stmt=self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$resetKey);		
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function updateUserPassword($userHash, $resetKey) {
		$this->sql="UPDATE usertable SET userpass = ? WHERE password_reset_key = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ss",$userHash, $resetKey);		
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
	}


	public function setResetKeyByEmail($resetKey, $email) {
		$this->sql="UPDATE usertable SET password_reset_key = ? WHERE email = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ss",$resetKey, $email);		
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
	}

	public function wipeResetKeyWithNewPass($userHash) {
		$this->sql='UPDATE usertable SET password_reset_key = "" WHERE userpass = ?';
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$userHash);		
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
	}

	public function wipeResetKey($resetKey) {
		$this->sql='UPDATE usertable SET password_reset_key = "" WHERE password_reset_key = ?';
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$resetKey);		
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			return 0;
		} else {
			return $this->stmt->affected_rows;
		}
	}

	public function getUserEmailByUserid($userid, $style=MYSQLI_ASSOC) {
		$this->sql = "SELECT email FROM usertable WHERE userid = ?;";
		$this->stmt=self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$userid);		
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}
}
?>
