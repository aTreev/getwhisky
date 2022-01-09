<?php 
require_once("menucrud.class.php");

/***************
 * Class that returns a list of current
 * product categories
 * Directions for use:
 *      Instantiate the class
 *      print the object
 ************************************/
class ProductCategoryList {
    private $categoryList = [];

    public function __construct() {
        $this->getCategoryList();
    }

    private function getCategoryList() {
        $source = new MenuCRUD();
        $this->categoryList = $source->getProductCategories();
    }

    public function __toString() {
        $html = "";
        $html.="<label for='product-category'>Product Category</label>";
        $html.="<select id='product-category' class='select-text'>";
        $html.="<option value='-1'>Please select a product category</option>";
        foreach($this->categoryList as $category) {
            $html.="<option value='".$category['id']."'>".$category['name']."</option>";
        }
        $html.="</select>";

        return $html;
    }
}
?>