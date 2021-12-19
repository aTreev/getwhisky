function prepareProductManagementPage() {
    /****
     * TODO:
     *  Add product search capability
     *  Add sorting option capability
     */
    // Get all product items
    const products = $(".product-management-item");

    // iterate through each and apply logic
    products.each(function(){
        // Local variables
        const productid = $(this).attr("id");
        const productName = $("#product-name-"+productid).text();
        const activeCheckbox = $("#active-"+productid);
        const featuredCheckbox = $("#featured-"+productid);
        const updateDiscountBtn = $("#update-discount-"+productid);
        const basePrice = $("#product-base-price-"+productid);
        const discountPrice = $("#discount-price-"+productid);
        const discountEndDatetime = $("#discount-end-datetime-"+productid)
        discountEndDatetime.attr("min", new Date().toISOString().split("Z")[0])
        const endDiscountBtn = $("#end-discount-"+productid);
        const addDiscountBtn = $("#add-discount-"+productid);

        // active checkbox toggle logic
        activeCheckbox.off();
        activeCheckbox.click(function(ev){
            if ($(this).prop("checked") == true) {
                if (!confirm(`Are you sure you want to activate product : ${productName}?`)) return ev.preventDefault();
                toggleProductActiveState(productid, productName, true);
            } 
            if ($(this).prop("checked") == false) {
                if(!confirm(`Are you sure you want to deactivate product : ${productName}?`)) return ev.preventDefault();
                toggleProductActiveState(productid, productName, false);
            }
        });

        // featured checkbox toggle logic
        featuredCheckbox.off();
        featuredCheckbox.click(function(ev){
            if ($(this).prop("checked") == true) {
                if (!confirm(`Are you sure you want set ${productName} as a featured product?`)) return ev.preventDefault();
                toggleProductFeaturedState(productid, productName, true);
            } 
            if ($(this).prop("checked") == false) {
                if(!confirm(`Are you sure you want to remove ${productName} from featured products?`)) return ev.preventDefault();
                toggleProductFeaturedState(productid, productName, false);
            }
        });



        // Update discount button logic
        updateDiscountBtn.off();
        updateDiscountBtn.click(function(){
            $(".form-feedback").remove();
    
            // validate fields
            let dv, dev = false;
            dv = checkNumberField(discountPrice, "Please provide a discount price");
            dev = checkDatetimeField(discountEndDatetime);

            if (dv && dev) {
                if (confirm(`Are you sure you wish to set a ${Math.floor(((basePrice.attr("price") - discountPrice.val()) / basePrice.attr("price")) * 100)}% discount for ${productName}`)) {
                    addProductDiscount(productid, discountPrice.val(), discountEndDatetime.val(), productName);
                }
            }
        });

        // End discount button logic
        endDiscountBtn.off();
        endDiscountBtn.click(function(){
            if (confirm(`Are you sure you wish to end the discount for ${productName}?`)) {
                endProductDiscount(productid, productName);
                // Construct the button to add a discount
                $("#product-stock-data-"+productid).html(`<div class='td-flex-center'><button class='add-action-btn' id='add-discount-${productid}'>Add discount</button></div>`);
                // Recall function to refresh event listeners
                prepareProductManagementPage();
            }
        });
        
        // Add discount button logic
        addDiscountBtn.off();
        addDiscountBtn.click(function(){
            // Construct the markup to create a new discount
            $("#product-stock-data-"+productid).html(`<div class='td-flex-center'><label class='container-label'>Discount price: &nbsp;<input type='number' id='discount-price-${productid}' step='0.01' /></label><label class='container-label'>End date: &nbsp;<input type='datetime-local' id='discount-end-datetime-${productid}' min='${new Date().toISOString().split("Z")[0]}'></label><button id='update-discount-${productid}'><i class='fas fa-wrench'></i>Save</button><button class='delete-action-btn' id='end-discount-${productid}'><i class='fas fa-hourglass-end'></i>End</button></div>`);
            // Recall function to refresh event listeners
            prepareProductManagementPage();
        });
    });
}


/****
 * Updates the active state of a product via the
 * product management ajax handler
 *********************/
function toggleProductActiveState(productid, productName, state) {
    $.ajax({
        url: "../php/ajax-handlers/product-management-handler.php",
        method: "POST",
        data: {function: 1, productid: productid}
    })
    .done(function(result){
        console.log(result);
        result = JSON.parse(result);
        if (result) {
            if (state == true) return new Alert(true, `<b>${productName}</b> activated.`);
            if (state == false) return new Alert(true, `<b>${productName}</b> deactivated.`);
        }
        if (!result) return new Alert(true, "Failed to update product active state, please refresh and retry.");
    });
}

/****
 * toggles the active state of a product via the
 * product management ajax handler
 *********************/
function toggleProductFeaturedState(productid, productName, state) {
    $.ajax({
        url: "../php/ajax-handlers/product-management-handler.php",
        method: "POST",
        data: {function: 2, productid: productid}
    })
    .done(function(result){
        result = JSON.parse(result);
        if (result) {
            if (state == true) return new Alert(true, `<b>${productName}</b> added to featured products.`);
            if (state == false) return new Alert(true, `<b>${productName}</b> removed from featured products list.`);
        }
        if (!result) return new Alert(true, "Failed to update product featured state, please refresh and retry.");
    });
}

/*****
 * Adds a discount to a product using the product management
 * ajax handler.
 * Additionally works to update a discount 
 *************************/
function addProductDiscount(productid, price, endDatetime, productName) {
    $.ajax({
        url: "../php/ajax-handlers/product-management-handler.php",
        method: "POST",
        data: {function: 3, productid: productid, price: price, endDatetime: endDatetime.replace("T", " ")}
    })
    .done(function(result){
        result = JSON.parse(result);

        if (result) return new Alert(true, `Discount set successfully for <b>${productName}</b>.`)
        if (!result) return new Alert(false, `Failed to set product discount, please refresh and try again.`)
    });
}

/****
 * Ends a product discount using the product management
 * ajax handler
 ********/
function endProductDiscount(productid, productName) {
    $.ajax({
        url: "../php/ajax-handlers/product-management-handler.php",
        method: "POST",
        data: {function: 4, productid: productid}
    })
    .done(function(result){
        if (result) return new Alert(true, `Discount removed for product <b>${productName}</b>`);
        if (!result) return new Alert(false, `Failed to remove product discount, please refresh and try again.`);

    })
}


function prepareProductManagementSearch() {
    const searchbar = $("#product-management-search");
    searchbar.keyup(function(){
        if ($(this).val().length > 0) {
            getProductsBySearch($(this).val());
        } else {
            getBaseProductManagementHtml();
        }
        
    })
}


function getProductsBySearch(str) {
    $.ajax({
        url: "../php/ajax-handlers/product-management-handler.php",
        method: "POST",
        data:{function: 5, searchString: str}
    })
    .done(function(result){
        result = JSON.parse(result);
        
        if (result.result == 1) {
            $("#product-management-table-body").html(result.html);
            prepareProductManagementPage();
        }
        if (result.result == 0) {
            $("#product-management-table-body").html("<p>No products found</p>");
            prepareProductManagementPage();
        }
    });
}

function getBaseProductManagementHtml() {
    $.ajax({
        url: "../php/ajax-handlers/product-management-handler.php",
        method: "POST",
        data: {function: 6}
    })
    .done(function(result){
        $("#product-management-table").html(JSON.parse(result));
        prepareProductManagementPage();
        prepareProductManagementSearch();
        $("#product-management-search").focus();
    });
}