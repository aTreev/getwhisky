<?php 
require_once("db.php");

class UserAddressCRUD {
    private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}

    public function getUserAddresses($userid, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM user_address_table WHERE userid = ?;";
        $this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$userid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function addNewAddress($address_id, $userid, $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county) {
        $this->sql = "INSERT INTO user_address_table VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ssssssssss",$address_id, $userid, $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county);
		$this->stmt->execute();
        return $this->stmt->affected_rows;
    }

    public function getExistingAddressIds($style=MYSQLI_ASSOC) {
        $this->sql = "SELECT address_id FROM user_address_table;";
        $this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }
}
?>
