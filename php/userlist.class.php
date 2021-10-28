<?php
// UserList class will use the getAllUsers method we just created to generate an array of users, and when output will generate
// a select box which can be used on the form.
require_once("usercrud.class.php");

class UserList {
	private $users=[], $source;
	
	public function __construct() {
		$this->source = new UserCRUD();
		$this->getUserList();
	}
	
	public function getUserList($orderby="username", $style=MYSQLI_ASSOC) {
		$this->users=$this->source->getAllUsers($orderby, $style);
	}
	
	public function __toString() {
		$string="<select name='userid'>";
		foreach($this->users as $user) {
			$string.="<option value='".$user['userid']."'>".$user['firstname']." ".$user['surname']." (".$user['username'].")"."</option>";
		}
		$string.="</select>";
		return $string;
	}
}
?>
