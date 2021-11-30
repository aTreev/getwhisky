/******************
 * The functions.js file contains functions that are global across all pages.
 * Also contains functions that are structured to be reused across the site
 * i.e. modals and password testing
 *********/
// TODO: chance to generalized prepareApp, move code to separate functions
function prepareMenu() {
    const mobileWidth = 768;
    // 834 works as pixel sizes a bit buggy between JS and CSS
    const searchBarBreakpoint = 835; 
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
    if($(window).width() <= searchBarBreakpoint) {
        $(".header-content").after($(".search-bar-container"));
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
        if ($(window).width() <= searchBarBreakpoint) {
            if (!searchBarMobile) {
                $(".header-content").after($(".search-bar-container"));
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
    })

    // Check for any cart notifications, display and add closeBtn functionality
    if ($(".cart-notification")) {
        $(".cart-notification").css("transform", "translateX(0%)")
        let hideTimeout = setTimeout(() => {
                $(".cart-notification").css("transform", "translateX(150%)");
            }, 5000);
        
        $(".cart-notification").on("mouseover", function(){
            clearTimeout(hideTimeout);
        })
        $(".cart-notification").on("mouseleave", function(){
            console.log("mouse leave");
            setTimeout(() => {
                $(".cart-notification").css("transform", "translateX(150%)");
            }, 5000);
        })

        $("#close-cart-notification").on("click", function(){
            $(".cart-notification").remove();
        })
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
}



function showModal(id, showOverlay) {
    $("#"+id).show();
    if (showOverlay) $(".page-overlay").show();
    $(document.body).toggleClass("prevent-scrolling-all");
    
    // Define a function for closing modal when out of focus
    let closeModalListener = function(e) {
           if(!(($(e.target).closest("#"+id).length > 0 ) )){
            hideModal(id);
            // Unbind the event listener when the modal has been closed
            $(document).unbind("click", closeModalListener);
           }
    }
    // bind the close modal function to the document
    setTimeout(() => {
       $(document).bind("click", closeModalListener)
    }, 200);
}


function hideModal(id) {
    $("#"+id).hide();
    $(".page-overlay").hide();
    $(document.body).toggleClass("prevent-scrolling-all");
}

/**************************
 * Tests a password against regex to ensure that
 * a minimum complexity is met, takes in the password
 * input element as an argument.
 * Appends a password strength indicator to the password input
 * returns the password strength as float.
 ************************************************************/
function testPasswordStrength(passwordField) {
    let password = passwordField.val();
    console.log(password);
    let feedbackcolour;
    // Get the total length of password, baseline strength of 1 for a 6 character password
    let passtr=(password.length<6)?1:password.length/2;
    // Use Regex to find what types of characters, symbols and numbers used
    let hassymbol=((/[-!£$%^&*()_+|~=`{}\[\]:";'<>?,.\/]/).test(password))?1.5:0;
    let hasnumeric=((/[0-9]/).test(password))?1.4:0;
    let hasupper=((/[A-Z]/).test(password))?1.2:0;
    let haslower=((/[a-z]/).test(password))?1.1:0;
    // Calculate the overall relative strength of the password
    passwordStrength=passtr*(hassymbol+hasnumeric+hasupper+haslower);
    
    if(passwordStrength>24) { passwordStrength=24; }
    // Yellow colour for medium strength passwords
    if(passwordStrength>8) { feedbackcolour="#FF0"; }
    // Green colour for strong passwords
    if(passwordStrength>16) { feedbackcolour="#0F0"; }
    // Red for weak
    if(passwordStrength<8) { feedbackcolour="#F00";}
    console.log(passwordStrength);
    var fbtarget=document.getElementById("password-feedback");
    if(!fbtarget) {
        var fb=document.createElement("p");
        fb.setAttribute("id", "password-feedback");
        fb.classList.add("password-indicator");
        passwordField.after(fb);
        fbtarget=document.getElementById("password-feedback");
        fbtarget.style.display="block";
        fbtarget.style.height="1em";
    }
    fbtarget.style.backgroundColor=feedbackcolour;
    fbtarget.style.width=passwordStrength+"em"

    return passwordStrength;
}