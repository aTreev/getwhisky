function prepareMenu() {
    const mobileWidth = 768;
    /**********
     * Initial check for mobile or desktop
     */
    if ($(window).width() < mobileWidth) {
        setMobileMenu();
    } else {
        setDesktopMenu();
        $("#product-menu-container").show();
    }
    
    /***************
     * Check whether the screen is small enough to switch menu after resize
     ***/
    $(window).resize(function(){
        // remove any menu specific properties
        $(".menu-overlay").hide();
        $("body").removeClass("prevent-scrolling");

        if ($(window).width() < mobileWidth) {
            $("#product-menu-container").css("transform", "translateX(-100%)");
            $("#product-menu-container").css("display","none");
            setMobileMenu();
        } else {
            setDesktopMenu();
            $("#product-menu-container").css("transform", "translateX(0%)");
            $("#product-menu-container").css("display","block");
        }
    });

    /******************
     * Add an event listener to the body to allow menu to open when the menu-button is clicked
     * and closed when anywhere else on the body is clicked
     ***/
    $(document).on("click", function(e){
        if ($(window).width() < mobileWidth) {
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
        
    })

    // Check for any cart notifications
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