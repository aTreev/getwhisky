function prepareProductCreationPage() {

    // Apply logic to the category select
    $("#product-category").change(function(){
        const categoryid = $(this).val();
        getAttributeList(categoryid).then(function(result){
            $("#product-attribute-selection").html(result.html);
        });
    });


    $("#product-image").change(function(){
        console.log("changed");
        const image = $(this)[0].files[0];
        if (image) {
            $("#product-image-preview").attr("src", URL.createObjectURL(image));
        }
    });

    // Save button majority of page logic here
    $("#save-product").click(function(){
        $(".form-feedback").remove();
        const nameField = $("#product-name");
        const typeField = $("#product-type");
        const priceField = $("#product-price");
        const stockField = $("#product-stock");
        const descField = $("#product-desc");
        const imageField = $("#product-image");
        const categorySelectField = $("#product-category");


        // Product required detail validation
        let nameValid, typeValid, priceValid, stockValid, descValid, imageValid, categorySelected = false;

        nameValid = checkFieldEmpty(nameField, "Please provide a product name", 90);
        typeValid = checkFieldEmpty(typeField, "Please enter a product type", 30);
        priceValid = checkNumberField(priceField, "Please provide a price", [0, 99999])
        stockValid = checkNumberField(stockField, "Please enter an initial stock value", [-1, null]);
        descValid = checkFieldEmpty(descField, "Please provide a product description");
        imageValid = checkFileField(imageField, "Please provide a product image", ["png", "jpg", "jpeg", "webp"])
        categorySelected = checkSelectField(categorySelectField, "Please choose a product category");

        // Product Attribute logic
        const productAttributes = $("[name=product-attribute]");
        const attributeValues = new Map();

        productAttributes.each(function(){
            const attributeId = $(this).attr("attribute-id");
            const attributeValue = $(this).val();

            if (attributeValue != -1) {
                attributeValues.set(attributeId, attributeValue);
            }
        });
        

        // All required fields present
        if (nameValid && typeValid && priceValid && stockValid && descValid && imageValid && categorySelected) {

            // Confirm product creation without filters
            if (attributeValues.size == 0) {
                if (confirm("are you sure you want to create a product without attributes? (it wont appear on any product filters)")) {
                    showModal("product-success-modal", true);
                }
            }
        }
    });
}


function getAttributeList(categoryid) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/product-creation-handler.php",
            method: "POST",
            data: {function: 1, categoryid: categoryid}
        })
        .done(function(result){
            resolve(JSON.parse(result));
        })
    });
}