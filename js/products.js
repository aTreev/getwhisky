const productsMobileBreakpoint = 830;
const categoryId = $("#category_id").val();
let selectedFilterValues = new Map();
let selectedSortValue;
let limit = 50;
/************
 * Convert this to use globals that are populated
 * and passed to the getproducts function
 ****************************************/
function prepareProductsPage() {
    getProducts(categoryId);
    prepareProductFilters();
    prepareProductSortOptions();
    makeFilterSectionInteractive();
    
}

/****************
 * Adds a click listener that toggles whether the filter options are visible
 * Hides all by default
 ******/
function makeFilterSectionInteractive() {
    $(".filter-header").on("click",function(){
        $(this).next().toggleClass("max-height-0");
        $(this).children().last().toggleClass("fas fa-plus");
        $(this).children().last().toggleClass("fas fa-minus");
    })
    if ($(window).width() <= productsMobileBreakpoint) {
        $(".filter-header").next().addClass("max-height-0");
    } else {
        $(".filter-header").children().last().toggleClass("fas fa-plus");
        $(".filter-header").children().last().toggleClass("fas fa-minus");
    }
    
    //Filter dropdown clicked
    $(".filter-item-header").on("click",function(){
        $(this).next().toggleClass("filter-item-options-show");
        $(this).children().last().toggleClass("fas fa-plus");
        $(this).children().last().toggleClass("fas fa-minus");

    });
}

/****************
 * Function gets all the selected product filters
 * Ensures that only one option of each filter can be selected
 * Calls the getProducts() function and passes the selected filters as an array
 *******/
function prepareProductFilters() {
    const numAttributesOnPage = document.getElementsByClassName("filter-item-options").length;
    for(let i = 0; i <= numAttributesOnPage; i++) {
        // Checkbox code graciously taken from bPratik at https://stackoverflow.com/questions/9709209/html-select-only-one-checkbox-in-a-group
        // I don't fully understand how it works but I understand it in the context of how I want it to work so it stays...
        $("[name=attribute_value"+i+"]").click(function(){
            const currentClick = $(this);
            // if checkbox item is checked
            if (currentClick.is(":checked")) {
                // get the group of attribute_value+number
                const group = $("[name=attribute_value"+i+"]");
                // set all checkboxes in group to unchecked
                group.prop("checked", false);
                // set specific checkbox to checked
                currentClick.prop("checked", true);
                // enter the value into the map
                selectedFilterValues.set(currentClick.attr("attribute_id"), currentClick.val());
                // Hide filters for UX improvement
                if ($(window).width() <= productsMobileBreakpoint) $(".filter-header").next().addClass("max-height-0");
            } else {
                // if checkbox item is unchecked
                // remove checked from clicked checkbox
                currentClick.prop("checked", false);
                // remove value from map
                selectedFilterValues.delete(currentClick.attr("attribute_id"));
            }
            // convert map back to array and send the attribute values to the ajax product filter
            getProducts(categoryId, Array.from(selectedFilterValues.values()), selectedSortValue)

        });
    }
}

/*************
 * Function provides functionality to the sorting buttons,
 * calls the getProducts() function on click and highlights
 * the selected value
 *******************/
function prepareProductSortOptions() {

    // Add a click event to each of the sorting options
    $("[name=sort]").each(function(){
        $(this).click(function(){
            // set the global product sorting option to selected value
            selectedSortValue = $(this).attr("sort-option");
            $("[name=sort]").css("font-weight", "400");
            $(this).css("font-weight", "700");
            getProducts(categoryId, Array.from(selectedFilterValues.values()), selectedSortValue);
        });
    })
}


/*******************
 * Function retrieves products from PHP, passing any filters via POST
 * Receives HTML markup and a product count if a filter has been selected
 * Provides the add to cart functionality to buttons every time the function
 * is called as products are entirely loaded in again
 *********************/
function getProducts(categoryId, attributeValues, selectedSortValue) {
    $.ajax({
        // Ajax parameters
        url:"../php/ajax-handlers/products-page-handler.php",
        method:"POST",
        data: {function: 1, categoryId: categoryId, attributeValues: attributeValues, sortOption: selectedSortValue},
        beforeSend: function(){
            $("#product-root").html("<img style='display:block;margin:auto;' src='/assets/loader.gif'>");
            $("#product-root").css("display", "block"); // set display to block, centers loader and content
        }
        // Include callback for status codes?

        // Ajax complete
    }).done(function(result){
        if (result) {
            result = JSON.parse(result);
            setTimeout(() => {
                $("#product-root").css("display", "grid"); // set style to grid, displays products in grid
                $("#product-count").html("")
                $("#product-root").html(result.html);   // set html to received html

                // Set count if received
                if (result.count >= 0) {
                    if (result.count == 0 || result.count > 1) $("#product-count").html("<p>"+result.count+" products found with selected filters</p>")
                    else $("#product-count").html("<p>"+result.count+" product found with selected filters</p>")
                }

                $("[name='add-to-cart']").on("click", function(){
                    let productId = $(this).prev().val();
                    addToCart(productId);
                });
            
            }, 200);
        }
    });
}

function addToCart(productId) {

    $.ajax({
        url:"../php/ajax-handlers/products-page-handler.php",
        method:"POST",
        data: {function: 2, productId: productId}
    }).done(function(result){
        console.log(result);
        result = JSON.parse(result);
        // added to cart
        if (result.result == 1) {
            $(".cart-count").html(result.cartCount);
            new Alert(true, "Item added to basket <a href='/cart.php'>view basket</a>");
        }
        // Insufficient stock
        if (result.result == 2) {
            new Alert(false, "Unable to add to cart due to item shortage");
        }
        // invalid product id supplied
        if (result.result == 3) {
            new Alert(false, "We were unable to find that product, please try again");
        }
    });
}