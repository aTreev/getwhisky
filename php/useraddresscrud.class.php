<?php 
require_once("db.php");

class UserAddressCRUD {
    private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}

    public function getUserAddresses($userid, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM user_address_table WHERE userid = ? ORDER BY date_time_added ASC;";
        $this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$userid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }

    public function addNewAddress($addressid, $userid, $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county) {
        $d = new DateTime();
        $dateTimeAdded = $d->format("Y-m-d H:m:s");

        $this->sql = "INSERT INTO user_address_table VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?);";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("sssssssssss",$addressid, $userid, $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county, $dateTimeAdded);
		$this->stmt->execute();
        return $this->stmt->affected_rows;
    }

    public function updateUserAddress($addressid, $userid, $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county) {
        $this->sql = "UPDATE user_address_table SET identifier=?, full_name=?, telephone=?, postcode=?, line1=?, line2=?, city=?, county=? WHERE address_id=? AND userid=?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ssssssssss", $identifier, $fullName, $phoneNumber, $postcode, $line1, $line2, $city, $county, $addressid, $userid);
		$this->stmt->execute();
        return $this->stmt->affected_rows;
    }

    public function deleteUserAddress($addressid, $userid) {
        $this->sql = "DELETE FROM user_address_table WHERE address_id=? AND userid=?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ss", $addressid, $userid);
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

    public function getUserAddressById($addressid, $userid, $style=MYSQLI_ASSOC) {
        $this->sql = "SELECT * FROM user_address_table WHERE address_id = ? AND userid = ?;";
        $this->stmt = self::$db->prepare($this->sql);
        $this->stmt->bind_param("ss", $addressid, $userid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
    }
}
?>
