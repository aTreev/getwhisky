function prepareCartPage() {
    addPageEventListeners();
    
}

function addPageEventListeners() {
    $("[name='update-qty']").on("click", function(){
        let quantity = $(this).parent().prev().children()[1].value;
        let productId = $(this).parent().prev().children()[2].value;
        if (quantity > 0) {
            updateCartQuantity(productId, quantity);
        } else {
            removeFromCart(productId)
        }
    });

    $("[name='remove-from-cart']").on("click", function(){
        let productId = $(this).parent().prev().children()[2].value;
        removeFromCart(productId)
    });
}


function updateCartQuantity(productId, quantity) {
    $.ajax({
        url: "../php/ajax-handlers/cart-handler.php",
        method:"POST",
        data: {function: 1, productId: productId, quantity: quantity}

    }).done(function(result){
        result = JSON.parse(result);
        // .result = 1 success
        if (result.result == 1) {
            // Update html and eventListeners
            $("#cart-container").html(result.html);
            $(".cart-count").html(result.cartCount);
            addPageEventListeners();
            new Alert(true, "Item quantity updated!");
        }
        // insufficient stock
        if (result.result == 2) {
            new Alert(false, "The selected quantity is unavailable");
        }
        // Invalid product id supplied
        if (result.result == 3 || result.result == 4) {
            new Alert(false, "Error processing request, please try again!");
        }

    });
}


function removeFromCart(productId) {
    $.ajax({
        url: "../php/ajax-handlers/cart-handler.php",
        method:"POST",
        data: {function: 2, productId: productId}

    }).done(function(result){
        result = JSON.parse(result);
        // .result = 1 successfully removed
        if (result.result == 1) {
            // Update html and eventListeners
            $("#cart-container").html(result.html);
            $(".cart-count").html(result.cartCount);
            addPageEventListeners();
            new Alert(true, "Item removed from cart");
        } else {
            new Alert(false, "Error processing request, please try again!");
        }
    })
}