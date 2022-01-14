<?php 
require_once("menucrud.class.php");
class CategoryAttributeList {
    private $attributeList = [];
    private $categoryid;
    private $menuCRUD;


    public function __construct($categoryid) {
        $this->menuCRUD = new MenuCRUD();
        $this->setCategoryId($categoryid);
        $this->retrieveCategoryAttributeList();
    }


    private function setCategoryId($categoryid) { $this->categoryid = $categoryid; }
    private function setAttributeList($attributeList) { $this->attributeList = $attributeList; }

    private function getCategoryId() { return $this->categoryid; }
    private function getAttributeList() { return $this->attributeList; }

    
    private function retrieveCategoryAttributeList() {
        $this->setAttributeList($this->menuCRUD->getProductFiltersByCategoryId($this->getCategoryId()));
    }

    public function displayCategoryAttributeList() {
        $html = "";
        $result = 0;
        if ($this->getAttributeList()) {
            $result = 1;
            $html.="<div class='form-header' style='margin-top:20px;'>";
                $html.="<h4>Product Attribute selection</h4>";
                $html.="<p>The attributes selected below will be displayed on the product page as product details and will also be used for selection through product filters</p>";
            $html.="</div>";
            $html.="";
            foreach($this->getAttributeList() as $attribute) {
                $attrValues = $this->menuCRUD->getAttributeValuesByAttributeId($attribute['id']);
                $html.="<div class='input-container-100'>";
                    $html.="<label for='product-attribute'>".$attribute['title']."</label>";
                    $html.="<select class='select-text' attribute-id='".$attribute['id']."' name='product-attribute'>";
                        $html.="<option value='-1' style='font-size:1.4rem;'>None / Select an option</option>";
                        foreach($attrValues as $attrValue) {
                            $html.="<option value='".$attrValue['id']."' >".$attrValue['value']."</option>";
                        }
                    $html.="</select>";
                $html.="</div>";
            }
        } else {
            $html.="<p style='padding:20px;opacity:0.8;font-style:italic;'>No attributes available for this category</p>";
        }

        return ['result' => $result, 'html' => $html];
    }
}

?>