<?php
require_once("menucrud.class.php");
/*****************
 * Menu class
 * 	Retrieves the header navigation from the database
 * 	Takes in the usertype to only provide links relevant to
 * 	the user
 **************************************************/
class Menu {
	private $menulist=[];
	
	public function __construct($menulevel) {
		$this->setMenuItems($menulevel);
	}
	
	private function setMenuItems($menulevel) {
		$source=new MenuCRUD();
		$this->menulist=$source->getMenu($menulevel);
	}

	public function getMenuItems() {
		return $this->menulist;
	}
}
?>
