/******************
 * The functions.js file contains functions that are global across all pages.
 * Also contains functions that are structured to be reused across the site
 * i.e. modals and password testing
 *********/
// TODO: chance to generalized prepareApp, move code to separate functions
function prepareMenu() {
    // 834 works as pixel sizes a bit buggy between JS and CSS
    const mobileWidth = 835; 
    let searchBarMobile;
    let searchBarDesktop

    // Prep the product search
    prepareProductSearch();
    
    /**********
     * Initial check for mobile or desktop
     *********************/
    if ($(window).width() <= mobileWidth) {
        setMobileMenu();
    } else {
        setDesktopMenu();
        $("#product-menu-container").show();
    }

    /*************
     * Initial check for ideal search bar placement
     ***********************/
    if($(window).width() <= mobileWidth) {
        $("#product-menu-container").after($(".search-bar-container"));
        searchBarMobile = true;
        searchBarDesktop = false;
        $(".search-bar-container").show();
    } else {
        $("#getwhisky-logo-link").after($(".search-bar-container"));
        searchBarDesktop = true;
        searchBarMobile = false; 
        $(".search-bar-container").show();
    }
    
    /***************
     * Check whether the screen is small enough to switch menu after resize
     ************************/
    $(window).resize(function(){
        // remove any menu specific properties
        $(".menu-overlay").hide();
        $("body").removeClass("prevent-scrolling");

        if ($(window).width() <= mobileWidth) {
            $("#product-menu-container").css("transform", "translateX(-100%)");
            $("#product-menu-container").css("display","none");
            setMobileMenu();
        } else {
            setDesktopMenu();
            $("#product-menu-container").css("transform", "translateX(0%)");
            $("#product-menu-container").css("display","block");
        }

        /***************
         * Changes the position of the search bar depending on screen size
         * Does a check prior to changing to prevent unnecessary dom manipulation
         *********************/
        if ($(window).width() <= mobileWidth) {
            if (!searchBarMobile) {
                $("#product-menu-container").after($(".search-bar-container"));
                searchBarMobile = true;
                searchBarDesktop = false;
            }
        } else {
            if (!searchBarDesktop) {
               $("#getwhisky-logo-link").after($(".search-bar-container"));
                searchBarDesktop = true;
                searchBarMobile = false; 
            }
            
        }
    });

    /******************
     * Add an event listener to the body to allow menu to open when the menu-button is clicked
     * and closed when anywhere else on the body is clicked
     ******/
    $(document).on("click", function(e){
        // Click listener for mobile menu
        if ($(window).width() <= mobileWidth) {
            if (e.target.classList.contains("product-menu-button")) {
                // Menu button clicked show menus and prevent scrolling
                $("#product-menu-container").css({"transform":"translateX(0%)"});
                $(".menu-overlay").show();
                $("body").addClass("prevent-scrolling-menu");
            } else {
                // Body clicked, hide menus and re-allow scrolling
                $("#product-menu-container").css("transform", "translateX(-100%)");
                $(".menu-overlay").hide();
                $("body").removeClass("prevent-scrolling-menu");
            }
        }
        
        // Click listener for search results
        if (!e.target.classList.contains("product-search-bar")) {
            $("#search-results").hide();
        }
    });

    // Check for any cart notifications, display and add closeBtn functionality
    if ($(".cart-notification")) {
        $(".cart-notification").css("transform", "translateX(0%)")
        let hideTimeout = setTimeout(() => {
                $(".cart-notification").css("transform", "translateX(150%)");
            }, 5000);
        
        $(".cart-notification").on("mouseover", function(){
            clearTimeout(hideTimeout);
        });
        $(".cart-notification").on("mouseleave", function(){
            console.log("mouse leave");
            setTimeout(() => {
                $(".cart-notification").css("transform", "translateX(150%)");
            }, 5000);
        });

        $("#close-cart-notification").on("click", function(){
            $(".cart-notification").remove();
        });
    }
}

// Sets the menu layout for mobile
function setMobileMenu() {
    let menuButton = $("#product-menu-button");
    let menu = $("#product-menu");
    let menuContainer = $("#product-menu-container");
    menuButton.show();
    menuContainer.hide();
    menuContainer.removeClass("product-menu-container");
    menuContainer.addClass("product-menu-container-mobile");
    menu.removeClass("product-menu-list");
    menu.addClass("product-menu-list-mobile");
    menuContainer.show();
    $(".header-logo").attr("src", "/assets/getwhisky-logo-small.png");
    $(".header-nav-link").hide();
    $(".header-menu ul").css("gap", "40px");

}

// Sets the menu layout for desktop
function setDesktopMenu() {
    let menuButton = $("#product-menu-button");
    let menu = $("#product-menu");
    let menuContainer = $("#product-menu-container");
    menuButton.hide();
    menuContainer.addClass("product-menu-container");
    menuContainer.removeClass("product-menu-container-mobile");
    menu.addClass("product-menu-list");
    menu.removeClass("product-menu-list-mobile");
    $(".header-logo").attr("src", "/assets/getwhisky-logo-lowercase.png")
    $(".header-nav-link").show();
    $(".header-menu ul").css("gap", "15px");

}


/**************
 * Prepares the product search feature by adding a keyup eventlistener
 * to the page search bar
 * Sends an ajax request to retrieve products and display them asynchronously
 ***********************************************/
function prepareProductSearch() {
    $("#product-search-bar").on("keyup", function(){
        // Guard clause to prevent backend query on empty string also resets html
        if ($(this).val().length < 1) {
            $("#search-results").html(""); 
            $("#search-results").hide();
            return;
        }
        let searchQuery = $(this).val();
        // Show search results container
        $("#search-results").show();
        
        // AJAX request
        $.ajax({
            url: "../php/ajax-handlers/product-search-handler.php",
            method: "POST",
            data: {location: "search-bar", searchQuery: $(this).val()}

        }).done(function(result){
            // Parse result and perform appropriate action
            result = JSON.parse(result);
            if (result.result == 1) {
                // Products found show result and link
                $("#search-results").html(result.html);
                $("#search-results").append("<div class='search-result-item'><p style='padding: 10px;'>View all results</p><a href='/product-search.php?q="+searchQuery+"' class='sr-wrapper-link'><span></span></a></div>");
            } else {
                // None found, notify user
                $("#search-results").html("<p style='padding:20px;'>No product suggestions</p>")
            }
        });
    });

    $("#search-icon").on("click", function(){
        console.log("HELLO?")
        const searchQuery = $(this).prev().val();
        if (searchQuery.length == 0) return;
        window.location.href=`/product-search.php?q=${searchQuery}`;
    });
}



function showModal(id, showOverlay) {
    $("#"+id).show();
    if (showOverlay == true) {
        $(".page-overlay").show();
        $(document.body).addClass("prevent-scrolling-all");
    }
    
    // Define a function for closing modal when out of focus
    let closeModalListener = function(e) {
           if(!(($(e.target).closest("#"+id).length > 0 ))){
            hideModal(id);
            // Unbind the event listener when the modal has been closed
            $(document).unbind("mousedown", closeModalListener);
           }
    }
    // bind the close modal function to the document
    setTimeout(() => {
        // Bind to mousedown to prevent dragging outside modal closing it
       $(document).bind("mousedown", closeModalListener)
    }, 50);
}


function hideModal(id) {
    $("#"+id).hide();
    $(".page-overlay").hide();
    $(document.body).removeClass("prevent-scrolling-all");
}