<?php
require_once("menucrud.class.php");
class Menu {
	private $menulist=[];
	
	public function __construct($menulevel) {
		$this->setMenuItems($menulevel);
	}
	
	private function setMenuItems($menulevel) {
		$source=new MenuCRUD();
		$this->menulist=$source->getMenu($menulevel);
	}
	
	public function __toString() {
		$menustr="";
		foreach($this->menulist as $menuitem) {
			$menustr.="<li><a href='".$menuitem['url']."'>".$menuitem['pagename']."</a></li>";
		}
		return $menustr;
	}
}
?>
