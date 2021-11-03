function prepareProductsPage() {
    categoryId = $("#category_id").val();
    getProducts(categoryId);
    // add more values as filter options are selected
    prepareProductFilters();
}

/****************
 * Function gets all the selected product filters
 * Ensures that only one option of each filter can be selected
 * Calls the getProducts() function and passes the selected filters as an array
 */
function prepareProductFilters() {
    const numAttributesOnPage = document.getElementsByClassName("filter-item-options").length;
    let selectedFilterValues = new Map();
    for(let i = 0; i < numAttributesOnPage; i++) {
        // Checkbox code graciously taken from bPratik at https://stackoverflow.com/questions/9709209/html-select-only-one-checkbox-in-a-group
        // I don't fully understand how it works but I understand it in the context of how I want it to work so it stays...
        $("[name=attribute_value"+i+"]").click(function(){
            let currentClick = $(this);
            // if checkbox item is checked
            if (currentClick.is(":checked")) {
                // get the group of attribute_value+number
                let group = $("[name=attribute_value"+i+"]");
                // set all checkboxes in group to unchecked
                group.prop("checked", false);
                // set specific checkbox to checked
                currentClick.prop("checked", true);
                // enter the value into the map
                selectedFilterValues.set(currentClick.attr("attribute_id"), currentClick.val());
            } else {
                // if checkbox item is unchecked
                // remove checked from clicked checkbox
                currentClick.prop("checked", false);
                // remove value from map
                selectedFilterValues.delete(currentClick.attr("attribute_id"));
            }
            // convert map back to array and send the attribute values to the ajax product filter
            getProducts(categoryId, Array.from(selectedFilterValues.values()))
        });
    }
}

function getProducts(categoryId, attributeValues) {
    $(document).ajaxStart(function(){
        $("#product-root").html("<img style='display:block;margin:auto;' src='/assets/loader.gif'>");
    });
    $.ajax({
        // Ajax parameters
        url:"../php/ajax-handlers/products-page-handler.php",
        method:"POST",
        data: {function: 1, catid: categoryId, attribute_values: attributeValues}
        // Include callback for status codes?

        // Ajax complete
    }).done(function(result){
        if (result) {
            $("#product-root").html(result);
        } else {
            $("#product-root").html("<div class='no-products-found'><h2>We couldn't find any products!</h2><p>Try reducing the number of filters</p></div>");
        }
    })
}