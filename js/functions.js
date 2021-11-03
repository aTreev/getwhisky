function prepareMenu() {
    /**********
     * Initial check for mobile or desktop
     */
    if ($(window).width() < 639) {
        setMobileMenu();
    } else {
        setDesktopMenu();
        $("#product-menu-container").show();
    }
    
    /***************
     * Check whether the screen is small enough to switch menu after resize
     */
    $(window).resize(function(){
        // remove any menu specific properties
        $(".page-overlay").hide();
        $("body").removeClass("prevent-scrolling");

        if ($(window).width() < 639) {
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
     */
    $(document).on("click", function(e){
        if (e.target.classList.contains("product-menu-button")) {
            // Menu button clicked show menus and prevent scrolling
            $("#product-menu-container").css({"transform":"translateX(0%)"});
            $(".page-overlay").show();
            $("body").addClass("prevent-scrolling");
        } else {
            // Body clicked, hide menus and re-allow scrolling
            $("#product-menu-container").css("transform", "translateX(-100%)");
            $(".page-overlay").hide();
            $("body").removeClass("prevent-scrolling");
        }
    })
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

function showModal(id, showOverlay=false) {
    $("#"+id).show();
    if (showOverlay) $(".page-overlay").show();
    let escapeListener = document.addEventListener("keyup", function(e){
        if (e.key === "Escape") {
            hideModal(id, escapeListener);
        }
    })
}

function hideModal(id, escapeListener) {
    $("#"+id).hide();
    $(".page-overlay").hide();
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