function prepareProductManagementPage() {
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

        // active checkbox logic
        activeCheckbox.click(function(ev){
            if ($(this).prop("checked") == true) {
                if (!confirm(`Are you sure you want to activate product : ${productName}?`)) return ev.preventDefault();
            } 
            if ($(this).prop("checked") == false) {
                if(!confirm(`Are you sure you want to deactivate product : ${productName}?`)) return ev.preventDefault();
            }
            toggleProductActiveState(productid);
        });

        // featured checkbox logic
        featuredCheckbox.click(function(ev){
            if ($(this).prop("checked") == true) {
                if (!confirm(`Are you sure you want set ${productName} as a featured product?`)) return ev.preventDefault();
            } 
            if ($(this).prop("checked") == false) {
                if(!confirm(`Are you sure you want to remove ${productName} from featured products?`)) return ev.preventDefault();
            }
            toggleProductFeaturedState(productid);
        });


        
        // Update discount button logic
        updateDiscountBtn.click(function(ev){
            $(".form-feedback").remove();
    
            // validate fields
            let dv, dev = false;
            dv = checkNumberField(discountPrice, "Please provide a discount price");
            dev = checkDatetimeField(discountEndDatetime);

            if (dv && dev) {
                if (confirm(`Are you sure you wish to set a ${Math.floor(((basePrice.attr("price") - discountPrice.val()) / basePrice.attr("price")) * 100)}% discount for ${productName}`)) {
                    addProductDiscount(productid, discountPrice.val(), discountEndDatetime.val());
                }
            }
        });
    });
}


/****
 * Updates the active state of a product via the
 * product management ajax handler
 *********************/
function toggleProductActiveState(productid) {
    $.ajax({
        url: "../php/ajax-handlers/product-management-handler.php",
        method: "POST",
        data: {function: 1, productid: productid}
    })
    .done(function(result){
        console.log(result);
        result = JSON.parse(result);
        if (result) return new Alert(true, "Product active state updated successfully.");
        if (!result) return new Alert(true, "Failed to update product active state, please refresh and retry.");
    });
}

/****
 * Updates the active state of a product via the
 * product management ajax handler
 *********************/
function toggleProductFeaturedState(productid) {
    $.ajax({
        url: "../php/ajax-handlers/product-management-handler.php",
        method: "POST",
        data: {function: 2, productid: productid}
    })
    .done(function(result){
        result = JSON.parse(result);
        if (result) return new Alert(true, "Product featured state updated successfully.");
        if (!result) return new Alert(true, "Failed to update product featured state, please refresh and retry.");
    });
}

function addProductDiscount(productid, price, endDatetime) {
    console.log(endDatetime.replace("T", ":"));
}