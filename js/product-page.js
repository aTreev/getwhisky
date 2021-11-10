function prepareProductPage() {
    prepareProductTabs();
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
            new Alert(false, "Unable to add to cart due to stock shortage");
        }
    });
}