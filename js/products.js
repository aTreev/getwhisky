/***************************************
 * [Global variables]
 * These are used due to the need for multiple
 * method calls through different operations and
 * prevents the need for parameters
 *********************************/

/**** 
 * Viewport breakpoint, used for applying stylings
 * based on window width
 * 818 seems to work with 835 breakpoint in products.css
****/
const productsMobileBreakpoint = 835;

/*******
 * id of the current products category
 * used to retrieve products by category
 *******/
const categoryId = $("#category_id").val();

/******
 * Map of selected filter ids. Map used as it prevents values from
 * being duplicated due to the key/value structure
 **************/
let selectedFilterValues = new Map();

/**********
 * The currently selected products sort value
 * Latest (default), Price low > high, Price high > low
 *******************/
let selectedSortValue;

/**********
 * Amount of products that should be displayed
 * used as a method of pagination, increased by user
 * selecting to view more products default 20
 * Tweak this value here and in the getProducts() function
 ***************/
let limit = 8;

/**********
 * The number of products currently displayed on the page
 * Checked against the limit to determine how many products
 * to display
 ********************/
let productsDisplayed = 0;

/***************
 * The array of product html returned from PHP
 * In array format to allow the pagination
 *******************/
let productHtmlArray;


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
    //$("#filter-root").toggle();

    // Button to show filters
    $("#show-filters-btn").on("click",function(){
        $("#filter-root").toggle();
        $("#show-filters-btn").toggleClass("filters-btn-active");
    })
    
    // Check whether window is mobile or desktop and apply
    // the corresponding styling
    if ($(window).width() <= productsMobileBreakpoint) {
        $("#filter-root").hide();
        $(".filter-item-options").each(function(){
            $(this).removeClass("filter-item-options-show");
            $("#show-filters-btn").removeClass("filters-btn-active");
            $(this).prev().children().last().removeClass("fas fa-minus");
            $(this).prev().children().last().addClass("fas fa-plus");
        });
    } else {
        $(".filter-item-options").each(function(){
            $(this).addClass("filter-item-options-show");
            $(this).prev().children().last().removeClass("fas fa-plus")
            $(this).prev().children().last().addClass("fas fa-minus")
        });
    }

    // Check if window is resized and reset stylings
    $(window).resize(function(){
        if ($(this).width() <= productsMobileBreakpoint) {
            // mobile stylings
            $("#filter-root").hide();
            $(".filter-item-options").each(function(){
                $(this).removeClass("filter-item-options-show");
                $("#show-filters-btn").removeClass("filters-btn-active");
                $(this).prev().children().last().removeClass("fas fa-minus");
                $(this).prev().children().last().addClass("fas fa-plus");
            });
        } else {
            // desktop stylings
            $("#filter-root").show();
            $(".filter-item-options").each(function(){
                $(this).addClass("filter-item-options-show");
                $(this).prev().children().last().removeClass("fas fa-plus");
                $(this).prev().children().last().addClass("fas fa-minus");

            });
        }
    })
    // Filter item dropdown clicked
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
        // I don't fully understand the code but I understand how it works... so it stays.
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
                if ($(window).width() <= productsMobileBreakpoint) {
                    //$("#filter-root").hide();
                    //$("#show-filters-btn").toggleClass("filters-btn-active");
                }
            } else {
                // if checkbox item is unchecked
                // remove checked from clicked checkbox
                currentClick.prop("checked", false);
                // remove value from Map
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
            // Call get products with all global variables
            getProducts(categoryId, Array.from(selectedFilterValues.values()), selectedSortValue);
        });
    })
}


/*******************
 * Function retrieves products from PHP, passing any filters via POST
 * Receives an array of HTML markup and a product count if a filter has been selected
 * Provides the add to cart functionality to buttons every time the function
 * is called as products are entirely loaded in again
 *********************/
function getProducts(categoryId, attributeValues, selectedSortValue) {
    $.ajax({
        // Ajax parameters
        url:"../php/ajax-handlers/products-page-handler.php",
        method:"POST",
        data: {function: 1, categoryId: categoryId, attributeValues: attributeValues, sortOption: selectedSortValue},
        // Display loader
        beforeSend: function(){
            $("#product-root").html("<img style='display:block;margin:auto;' src='/assets/loader.gif'>");
            $("#product-root").css("display", "block"); // set display to block, centers loader and content
        }

        // Ajax complete
    }).done(function(result){
        if (result) {
            result = JSON.parse(result);
            setTimeout(() => {
                $("#product-root").css("display", "grid"); // set style to grid, displays products in grid
                $("#product-count").html(""); // Reset product count html
                $("#product-root").html(""); //Reset product div html
                $("#load-more").remove();
                // Reset global variables
                limit = 8;
                productsDisplayed = 0;
                productHtmlArray = result.html;

                // Display products
                displayProducts();

                // Set count if received
                if (result.count != null) {
                    if (result.count == 0 || result.count > 1) {
                        $("#product-count").html("<p>"+result.count+" products found with selected filters</p>");
                    }
                    else {
                        $("#product-count").html("<p>"+result.count+" product found with selected filters</p>");
                    }

                    if (result.count == 0) {
                        $("#product-root").css("display", "block");
                        $("#product-root").html("<h2 style='text-align:center;padding:40px;'>Oops! We couldn't find anything</h2><img style='width:200px;display:block;margin:auto;' src='../assets/product-images/no-products-found.jpg'><p style='text-align:center;margin-bottom:60px;'>Please try again with fewer filters or try using the search bar!</p>");
                    }
                } 
                
            }, 200);
        }
    });
}

/******
 * Scroll Event attached to the document that checks whether the user
 * has scrolled to the bottom of the page.
 * Calls the displayProducts() function when bottom of page is reached
 ******************/
$(window).scroll(function(){
    if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight-200) {
        displayProducts();
    }
});



// Displays products to page allows for pagination, 
// uses global variables and recursion to achieve functionality
function displayProducts() {
    for(let i = productsDisplayed; i < limit; i++) {
        $("#product-root").append(productHtmlArray[i]);
        productsDisplayed++;
    }
    limit = limit + 20;
    
}
