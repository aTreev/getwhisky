function prepareCartPage() {
    addPageEventListeners();
    prepareSuggestedProducts();
}

function addPageEventListeners() {

    /******
     * Add the required logic to each cart item
     **********/
    $(".cart-item").each(function(){
        const productid = $(this).attr("product-id");
        const productName = $(`#product-name-${productid}`).text();
        let updateTimer;

        /**********
         * Quantity number input
         * uses a timer setup to only update the quantity after a delay, after the user
         * has stopped changing the quantity, this prevents multiple AJAX requests
         ******************************/
        $(`#quantity-${productid}`).bind("input", function(){
            const maxQuantity = parseInt($(this).attr("max"));
            if (updateTimer) clearTimeout(updateTimer);  

            if ($(this).val() && $(this).val() > 0) {
                // Update the quantity after timer 
                updateTimer = setTimeout(() => {
                    if ($(this).val() > maxQuantity) {
                        $(this).val(maxQuantity);
                        new Alert(false, "Insufficient stock for desired quantity"); 
                    }
                    updateCartQuantity(productid, productName, $(this).val());
                }, 1000);
            }
        });


        $(`#remove-${productid}`).click(function(){
            removeFromCart(productid);
        })
    });
}

function prepareSuggestedProducts() {
    const suggestedProducts = $(".featured-product");
    if (!suggestedProducts) return;

    // Each suggested product logic
    suggestedProducts.each(function(){
        // Get product id and create add to cart buttons
        const productid = $(this).attr("product-id");
        $(this).append(`<button class='add-to-cart-btn' id='add-to-cart-${productid}'>Add to cart</button>`)

        // Newly created add to cart btn logic
        $(`#add-to-cart-${productid}`).click(function(){
            const thisButton = $(this);
            // Add to cart then
            addToCart(productid).then(function(result){
                if (result.result == 1) {
                    // result success update html and eventListeners
                    new Alert(true, "Item added to cart");
                    thisButton.text("Update basket");
                    thisButton.addClass("view-cart-btn");
                    $(`.featured-product[product-id='${productid}']`).css({"opacity": 0.7});

                    thisButton.off();
                    thisButton.click(function(){
                        $('.cart-position-container').nextAll().remove();
                        $(".cart-position-container").after("<img src='../assets/loader.gif' style='position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);'>");
                        // add ironic loader for UX
                        setTimeout(() => {
                            $('.cart-position-container').nextAll().remove();
                            $(".cart-position-container").after(result.html);
                            $(".cart-count").html(result.cartCount);
                        prepareCartPage();
                        }, 500);
                    })
                } 
                if (result.result == 2) {
                    new Alert(false, "Insufficient stock to add to cart");
                }
                else {
                    new Alert(false, "Error processing request, please try again");
                }
            });
        })
    });

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
                items:2,
            },
            768:{
                items:3,
            },
            1200:{
                items:4,
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

function updateCartQuantity(productId, productName, quantity) {
    console.log("fired");
    $.ajax({
        url: "../php/ajax-handlers/cart-handler.php",
        method:"POST",
        data: {function: 1, productId: productId, quantity: quantity}

    }).done(function(result){
        result = JSON.parse(result);
        if (result.result == 1) {
            // Update html and eventListeners
            $('.cart-position-container').nextAll().remove()
            $(".cart-position-container").after(result.html);
            $(".cart-count").html(result.cartCount);
            prepareCartPage();
            new Alert(true, `${productName} quantity updated`);
        }
        // insufficient stock
        if (result.result == 2) {
            new Alert(false, "The selected quantity is unavailable");
        }
        // Invalid product id supplied
        if (result.result == 3 || result.result == 4) {
            new Alert(false, "Error processing request, please try again");
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
            $('.cart-position-container').nextAll().remove()
            $(".cart-position-container").after(result.html);
            $(".cart-count").html(result.cartCount);
            prepareCartPage();

            new Alert(true, "Item removed from cart");
        } else {
            new Alert(false, "Error processing request, please try again");
        }
    })
}

function addToCart(productid) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/cart-handler.php",
            method:"POST",
            data: {function: 3, productid: productid}
    
        })
        .done(function(result){
            console.log(result);
            resolve(JSON.parse(result));
        });
    });
}