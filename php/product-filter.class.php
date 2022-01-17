<?php 
require_once("menucrud.class.php");
class ProductFilter {
    /*****************
     * Class ProductFilter
     * 
     * Generates a product filter menu based on the category id provided
     * Retrieves the titles and filter options on instantiation
     * 
     * Filter html constructed and returned by calling the __toString method
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

    /**************
     * The return html for filters
     * Checks to see if a filter option has any products attached to it
     * and displays only if a product can be obtained from the filter.
     *****************************/
    public function __toString() {
        $html = "";
        foreach ($this->getFilterTitles() as $filter) {
            // Filter group index - used to group filters together in JS
            $filterGroupIndex = 0;

            // display filter option headings
            $html.="<div class='filter-item'>";
                $html.="<div class='filter-item-header'>";
                    $html.="<h4>".htmlentities($filter['title'])."</h4>";
                    $html.="<i style='font-size:1.8rem;' class='fas fa-plus'></i>";
                $html.="</div>";
                // display filter options if they contain any products
                $html.="<div class='filter-item-options'>";
                foreach($this->getFilterValues() as $value) {
                    if (($value['count_products_with_value'] > 0) &&($value['attribute_id'] == $filter['id'])) {
                            $html.="<div class='filter-item-option'>";
                            $html.="<input id='".htmlentities($value['value'], ENT_QUOTES)."' type='checkbox' name='attribute-value-".$filterGroupIndex."' attribute_id='".htmlentities($filter['id'])."' value='".htmlentities($value['id'])."'>";
                            $html.="<label for='".htmlentities($value['value'], ENT_QUOTES)."'>".htmlentities($value['value'], ENT_QUOTES)."</label>";
                            $html.="</div>";
                    }
                }
                $html.="</div>";
            $html.="</div>";
            $filterGroupIndex++;
        }
        return $html;
    }
}
?>