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



    /********************
     * Displays attribute list on the category management page
     ****************************/
    public function displayAttributeListCategoryManagementPage() {
        $html = "<p style='font-style:italic;opacity:0.8;'>This category currently has no Filters</p>";
        $result = 0;
        if ($this->getAttributeList()) {
            $result = 1;
            $html="<h3 style='font-style:italic;opacity:0.8;font-weight:400;'>Category Filters</h3>";

            foreach($this->getAttributeList() as $attribute) {
                $html.="<div class='attribute-item'>";
                    $html.="<p>".$attribute['title']."</p>";
                    $html.="<div class='attribute-item-options'>";
                        $html.="<button attribute-id='".$attribute['id']."' name='remove-attribute'>Delete</button>";
                        $html.="<button attribute-id='".$attribute['id']."' name='manage-attribute'>Manage filter values</button>";
                    $html.="</div>";
                

                // Attribute values
                $html.="<div class='attribute-item-values' attribute-id='".$attribute['id']."'>";
                    // Get attribute's values
                    $attrValues = $this->menuCRUD->getAttributeValuesByAttributeId($attribute['id']);

                    // If current values display them
                    if ($attrValues) {
                    $html.="<h3 style='opacity:0.8;font-style:italic;font-weight:400;'>Filter Values for '".$attribute['title']."'</h3>";
                        // Iterate through attribute values
                        $html.="<div class='val-items'>";
                        foreach($attrValues as $attrValue) {
                            $html.="<div class='attribute-value-item'>";
                                $html.="<p>".$attrValue['value']."</p>";
                                $html.="<button attribute-id='".$attribute['id']."' attribute-value-id='".$attrValue['id']."' name='remove-attribute-val'>Delete</button>";
                            $html.="</div>";
                        }
                        $html.="</div>";
                    } else {
                        $html.="<p style='font-style:italic;opacity:0.8;'>This filter currently has no options</p>";
                    }
                    // Button to add new attribute value
                    $html.="<div class='filter-options' attribute-id='".$attribute['id']."'>";
                            $html.="<button class='action-btn' name='add-new-attribute-value' attribute-id='".$attribute['id']."' style='width:100%;'>Add new value</button>";
                    $html.="</div>";
                $html.="</div>";
                $html.="</div>";    
            }
            
        }
        // Button to add new attribute
        $html.="<div class='category-options'>";
                $html.="<button class='action-btn' id='new-attribute' style='width:100%;'>Add new filter</button>";
        $html.="</div>";
        return ['result' => $result, 'html' => $html];
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