<?php 
require_once("menucrud.class.php");
class ProductFilter {
    /*****************
     * CLASS ProductFilter
     * 
     * Generates a product filter menu for a category
     */
    private $categoryId;
    private $filterTitles = [];
    private $filterValues = [];

    public function __construct($categoryId) {
        $this->setCategoryId($categoryId);
        $this->getProductFilters();
    }

    private function setCategoryId($categoryId) { $this->categoryId = $categoryId; }
    private function setFilterTitles($filterTitles) {$this->filterTitles = $filterTitles; }
    private function setFilterValues($filterValues) {$this->filterValues = $filterValues; }

    private function getFilterTitles() { return $this->filterTitles; }
    private function getFilterValues() { return $this->filterValues; }



    // Retrieve both the filter titles and values of the categoryId
    private function getProductFilters() {
        $source = new MenuCRUD();
        $this->setFilterTitles($source->getProductFiltersByCategoryId($this->categoryId));
        $this->setFilterValues($source->getProductFilterValuesByCategoryId($this->categoryId));
    }

    // construct the filter html and return it
    public function getFilters() {
        $html = "";
        $html.=var_dump($this->getFilterTitles());
        $html.=var_dump($this->getFilterValues());
        return $html;
    }

    public function __toString() {
        $html = "";
        foreach ($this->getFilterTitles() as $filter) {
            // display filter option headings
            $html.="<div class='filter-item'>";
                $html.="<div class='filter-item-header'>";
                    $html.="<h4>".$filter['title']."</h4>";
                    $html.="<i style='font-size:1.8rem;' class='fas fa-plus'></i>";
                $html.="</div>";
                // display filter options if they contain any products
                $html.="<div class='filter-item-options'>";
                foreach($this->getFilterValues() as $value) {
                    if (($value['count_products_with_value'] > 0) &&($value['attribute_id'] == $filter['id'])) {
                            $html.="<div class='filter-item-option'>";
                            $html.="<input id='".$value['value']."'type='checkbox' name='attribute_value".$filter['id']."' attribute_id='".$filter['id']."' value='".$value['id']."'>";
                            $html.="<label for='".$value['value']."'>".$value['value']."</label>";
                            $html.="</div>";
                    }
                }
                $html.="</div>";
            $html.="</div>";
        }
        return $html;
    }
}
?>