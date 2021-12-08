/***************
 * This file contains repeated code from the products.js file.
 * Full documentation for the functions can be found in the file
 * mention above
 ******************************/

let limit = 8;
let productsDisplayed = 0;
let productHtmlArray;

function prepareSearchPage() {
    const searchQuery = $("#search-query").val();
    displaySearchResults(searchQuery);
    
    // Add a click event to each of the sorting options
    $("[name=sort]").each(function(){
        $(this).click(function(){
            // set the global product sorting option to selected value
            const selectedSortValue = $(this).attr("sort-option");
            $("[name=sort]").css("font-weight", "400");
            $(this).css("font-weight", "700");
            // Call get products with all global variables
            displaySearchResults(searchQuery, selectedSortValue)
        });
    })
}



    


/********
 * Handles the retrieval and display of search results on the search page
 * Product search bar itself is handled in the functions.js file since the
 * search bar is present across the entire site.
 **************/
function displaySearchResults(searchQuery, selectedSortValue) {
    $.ajax({
        url: "../php/ajax-handlers/product-search-handler.php",
        method: "POST",
        data: {location: "search-page", searchQuery: searchQuery, sortOption: selectedSortValue},

        // Ajax loader before products load
        beforeSend: function(){
            $("#product-root").html("<img style='display:block;margin:auto;' src='/assets/loader.gif'>");
            $("#product-root").css("display", "block");
        }

    }).done(function(result){
        result = JSON.parse(result);
        if(result.result == 1) {
            setTimeout(function(){
                $("#product-root").css("display", "grid");
                $("#product-count").html("<p>"+result.html.length+" products found</p>");
                $("#product-root").html("");
                // Reset global variables
                limit = 8;
                productsDisplayed = 0;
                productHtmlArray = result.html;

                displayProducts();
            }, 250)
            
        } else {
            // Display no products found html
            $("#product-count").html("<p>0 products found</p>");
            $("#product-root").css("display", "block");
            $("#product-root").html("<h2 style='text-align:center;padding:40px;'>Oops! We couldn't find anything</h2><img style='width:200px;display:block;margin:auto;' src='../assets/product-images/no-products-found.jpg'><p style='text-align:center;margin-bottom:50vh;'>Please try again using a different search term or try using the menu!</p>");
        }
    });
}


/******
 * Scroll Event attached to the document that checks whether the user
 * has scrolled to the bottom of the page.
 * Calls the displayProducts() function when bottom of page is reached
 ******************/
 $(window).scroll(function(){
    if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight-20) {
        displayProducts();
    }
});

// Displays products to page allows for pagination, 
// uses global variables and recursion to achieve functionality
function displayProducts() {
    if (productHtmlArray == null) return;
    for(let i = productsDisplayed; i < limit; i++) {
        $("#product-root").append(productHtmlArray[i]);
        productsDisplayed++;
    }
    limit = limit + 20;
    
}