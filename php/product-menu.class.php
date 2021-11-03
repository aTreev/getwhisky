<?php
require_once("menucrud.class.php");
/*****************
 * Menu class
 * 	Retrieves the header navigation from the database
 * 	Takes in the usertype to only provide links relevant to
 * 	the user
 **************************************************/
class ProductMenu {
	private $categories;
	
	public function __construct() {
		$this->setMenuItems();
	}
	
	private function setMenuItems() {
		$source=new MenuCRUD();
		$this->categories=$source->getProductMenu();
	}

	public function getMenuItems() {
		return $this->categories;
	}

	public function __toString() {
		$html = "<ul id='product-menu' class='product-menu-list' id='product-menu-list'>";
		foreach ($this->categories as $menuItem) {
			$html.="<li><a href='/products.php?catid=".$menuItem['id']."'>".$menuItem['name']."</a></li>";
		}
		$html.="</ul>";
		return $html;
	}
	
}
?>
