<?php
//The UserCRUD class, responsible for storing the data in a persistent data store, attempts to store the new data.
require_once("db.php");
class UserCRUD {
	private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}
	// SQL statement to select specific user from database
	public function getUserByName($username, $style=MYSQLI_ASSOC) {
		$this->sql="SELECT * FROM usertable WHERE username = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$username);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
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
	// returns error to message if email or username already exists
	public function storeNewUser($userid, $username,$firstname,$surname,$hash,$email,$dob, $vKey) {
		$this->sql="INSERT INTO usertable (userid,username,firstname,surname,userpass,email,dob, vkey) VALUES(?,?,?,?,?,?,?,?);";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ssssssss",$userid, $username,$firstname,$surname,$hash,$email,$dob, $vKey);
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			$errors="";
			if(strpos($this->stmt->error,'email')) {
				$errors.="Email address exists<br />";
			}
			if(strpos($this->stmt->error,'username')) {
				$errors.="Username exists<br />";
			}
			return $errors;
		} else {
			return $this->stmt->affected_rows;
		}
	}

	// updates a user's details
	public function updateUser($username,$firstname,$surname,$hash,$email,$dob,$usertype, $userid) {
		$this->sql="UPDATE usertable SET username=?, firstname=?, surname=?, userpass=?, email=?, dob=?, usertype=? WHERE userid=?;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ssssssis",$username,$firstname,$surname,$hash,$email,$dob,$usertype,$userid);		
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			$errors="";
			if(strpos($this->stmt->error,'email')) {
				$errors.="Email address exists<br />";
			}
			if(strpos($this->stmt->error,'username')) {
				$errors.="Username exists<br />";
			}
			return $errors;
		} else {
			return $this->stmt->affected_rows;
		}
	}

	// grabs all users ordered by the switch statement and returns the result
	public function getAllUsers($orderby="username", $style=MYSQLI_ASSOC) {
		//nb switch 'whitelist' prevents possibility of injection
		switch ($orderby) {
			case "username": $order="username";
						break;
			case "userid": $order="userid";
						break;
			case "surname": $order="surname";
						break;
			default: $order="username";
						break;
		}
		$this->sql="SELECT userid, username, firstname, surname FROM usertable ORDER BY ?;";
		$this->stmt= self::$db->prepare($this->sql);
		$this->stmt->bind_param("s", $order);
		$this->stmt->execute();
		$result=$this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	public function testUserEmail($username, $email, $style=MYSQLI_ASSOC) {
		$this->sql="SELECT * FROM usertable WHERE username = ? OR email = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ss",$username, $email);
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
}
?>
