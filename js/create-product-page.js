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
        const alcVolField = $("#product-alc-volume");
        const bottleSizeField = $("#product-bottle-size");
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

        // All required fields present
        if (nameValid && typeValid && priceValid && stockValid && descValid && imageValid && categorySelected) {

            // Product Attribute logic
            const productAttributes = $("[name=product-attribute]");
            const attributeValues = new Map();

            // Get attribute Ids and attributeValueIds
            productAttributes.each(function(){
                const attributeId = $(this).attr("attribute-id");
                const attributeValueId = $(this).val();

                if (attributeValueId != -1) {
                    attributeValues.set(attributeId, attributeValueId);
                }
            });


            // Confirm product creation without filters
            if (attributeValues.size == 0) {
                // No filters selected confirm upload
                if (!confirm("are you sure you want to create a product without attributes? (it wont appear on any product filters)")) return;

                uploadCreatedProduct(nameField.val(), typeField.val(), priceField.val(), stockField.val(), descField.val(), imageField[0].files[0], alcVolField.val(), bottleSizeField.val(), categorySelectField.val())
            } else {
                // Upload product
                uploadCreatedProduct(nameField.val(), typeField.val(), priceField.val(), stockField.val(), descField.val(), imageField[0].files[0], alcVolField.val(), bottleSizeField.val(), categorySelectField.val(), attributeValues)
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


function uploadCreatedProduct(name, type, price, stock, desc, image, alcoholVolume, bottleSize, categoryid, attributes) {
    console.log(arguments);
    console.log(image);
    // construct attributes in the format (array[0] - [0]=val1 [1]=val2)
    let attributeValueIds = [];
    if (attributes) {
        attributeValueIds = Array.from(attributes.values());
    }
    

    let formData = new FormData();
    formData.append("function", 2);
    formData.append("productName", name);
    formData.append("productType", type);
    formData.append("productPrice", price);
    formData.append("productStock", stock);
    formData.append("productDesc", desc);
    formData.append("productImage", image);
    formData.append("alcoholVolume", alcoholVolume);
    formData.append("bottleSize", bottleSize);
    formData.append("categoryid", categoryid);
    formData.append("attributeValueIds", attributeValueIds);
    
    $.ajax({
        url: "../php/ajax-handlers/product-creation-handler.php",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(result){
        console.log(result);
        result = JSON.parse(result);
        new Alert(true, result.message);
    });
}