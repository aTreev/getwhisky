function prepareProductPage() {
    prepareProductTabs();
    prepareOwlCarousel();

    $("[name='add-to-cart").on("click", function(){
        $(".form-feedback").remove();
        const productId = $("#product-id").val();
        const quantityField = $("[name='product-quantity']");
        let quantityValid = false;

        quantityValid = checkNumberField(quantityField, "Please enter a quantity", [0,15]);
        
        if (quantityValid) {
            addToCart(productId, quantityField.val());
        }
    });
}


function prepareProductTabs() {
    // Loop through each tab button
    $(".tab-btn").each(function(index){
        // Show attributes by default
        $("#attributes").css("display", "block");
        $("[name=attributes]").css({"border":"1px solid rgb(70, 125, 255)", "color":"rgb(70, 125, 255)"});
        // Add an eventListener to each tab button
        $(this).on("click", function(){
            $(".tab-btn").each(function(){
                $(this).css({"border":"1px solid lightgrey", "color":"black"});
            });
            $(this).css({"border":"1px solid rgb(70, 125, 255)", "color":"rgb(70, 125, 255)"});
            // Hide all tabs apart from the clicked tab
            $(".tab").each(function(){
                $(this).css("display", "none");
            });
            $("#"+$(this).attr("name")).css("display", "block");
        });
    });
}


function addToCart(productId, quantity) {

    $.ajax({
        url:"../php/ajax-handlers/product-page-handler.php",
        method:"POST",
        data: {function: 1, productId: productId, quantity: quantity}

    }).done(function(result){
        result = JSON.parse(result);
        // added to cart
        if (result.result == 0) {
            new Alert(false, "Unable to add product to cart, Please refresh and try again")
        }
        if (result.result == 1) {
            $(".cart-count").html(result.cartCount);
            new Alert(true, "Item added to basket <a href='/cart.php'>view basket</a>");
        }
        // Insufficient stock
        if (result.result == 2) {
            new Alert(false, "Unable to add specified quantity to cart");
        }
        // invalid product id supplied
        if (result.result == 3) {
            new Alert(false, "We were unable to find that product, please try again");
        }
    });
}

function prepareOwlCarousel() {
    $(".owl-carousel").owlCarousel({
        loop:true,
        items:4,
        margin: 10,
        nav:true,
        lazyLoad:true,
        //animateIn:true,
        responsive:{
            0:{
                items:2,
            },
            576:{
                items:3,
            },
            768:{
                items:4,
            },
            1200:{
                items:6,
            }
        }

    });
    // Using FA icons for navigation mapped to owl nav
    // Go to the next item
    $('.owl-nav-right').click(function() {
        $(".owl-carousel").trigger('next.owl.carousel');
    });
    // Go to the previous item
    $('.owl-nav-left').click(function() {
        $(".owl-carousel").trigger('prev.owl.carousel');
    });
}