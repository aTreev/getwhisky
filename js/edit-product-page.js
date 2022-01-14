const productid = $("#product-id").val();
function prepareProductEditPage() {
    prepareSingleFieldUpdateBtns();
    prepareCategoryOptions();
    prepareOverviewFunctionality();



}


function prepareSingleFieldUpdateBtns() {
    $("#product-name").keypress(function(e){ if(e.which == 13){ $("#update-product-name").click();}});
    $("#product-type").keypress(function(e){ if(e.which == 13){ $("#update-product-type").click();}});
    $("#product-price").keypress(function(e){ if(e.which == 13){ $("#update-product-price").click();}});
    $("#product-stock").keypress(function(e){ if(e.which == 13){ $("#update-product-stock").click();}});
    $("#product-desc").keypress(function(e){ if(e.which == 13){ $("#update-product-desc").click();}});
    $("#product-alc-volume").keypress(function(e){ if(e.which == 13){ $("#update-product-alc-volume").click();}});
    $("#product-bottle-size").keypress(function(e){ if(e.which == 13){ $("#update-product-bottle-size").click();}});


    $("#update-product-image").click(function(){
        const image = $("#product-image")[0].files[0];
        console.log(image);
    });

    $("#update-product-name").click(function(){
        $(".form-feedback").remove();
        if (checkFieldEmpty($("#product-name"),"Please provide a product name", 90)) {
            const name = $("#product-name").val();;
            updateProductName(name).then(function(result){
                if (result == 1) {
                    new Alert(result, `Product name updated to ${name}`);
                    $(".admin-header-text-container h1").text(`Getwhisky Editing - ${name}`)
                } else {
                    new Alert(result, `An error occurred, please try again`);
                } 

            });
        }
    });

    $("#update-product-type").click(function(){
        $(".form-feedback").remove();
        if (checkFieldEmpty($("#product-type"), "Please provide a product type", 80)) {
            const type = $("#product-type").val();
            updateProductType(type).then(function(result){
                if (result == 1) new Alert(result, `Product type updated to <b>${type}</b>`);
                else new Alert(result, `An error occurred, please try again`);
            });

        }
    });

    $("#update-product-price").click(function(){
        $(".form-feedback").remove();
        if (checkNumberField($("#product-price"), "Please enter a product price",[0,9999])) {
            const price = $("#product-price").val();
            updateProductPrice(price).then(function(result){
                if (result == 1) new Alert(result, `Product price update to  £${price}</b>`);
                else new Alert(result, `An error occurred, please try again`);
            });
        }
    });
    
    $("#update-product-stock").click(function(){
        $(".form-feedback").remove();
        if (checkNumberField($("#product-stock"), "Please enter a value for stock",[0,999])) {
            const stock = $("#product-stock").val();
            updateProductStock(stock).then(function(result){
                if (result == 1) new Alert(result, `Product stock updated to <b>${stock}</b>`)
            });
        }
    });

    $("#update-product-desc").click(function(){
        $(".form-feedback").remove();
        if (checkFieldEmpty($("#product-desc"), "Please enter a product description")) {
            const desc = $("#product-desc").val();
            updateProductDesc(desc).then(function(result){
                if (result == 1) new Alert(result, `Product description updated`);
                else new Alert(result, `An error occurred, please try again`);
            });
        }
    });

    $("#update-product-alc-volume").click(function(){
        $(".form-feedback").remove();
        if(checkFieldEmpty($("#product-alc-volume"), "Please enter an alcohol volume", 6)) {
            const alcVolume = $("#product-alc-volume").val();
            updateProductAlcVolume(alcVolume).then(function(result){
                if (result == 1) new Alert(result, `Alcohol Volume updated to ${alcVolume}`);
                else new Alert(result, `An error occurred, please try again`);
            });
        }
    });

    $("#update-product-bottle-size").click(function(){
        $(".form-feedback").remove();
        if (checkFieldEmpty($("#product-bottle-size"), "Please enter a bottle size", 15)) {
            const bottleSize = $("#product-bottle-size").val();
            updateProductBottleSize(bottleSize).then(function(result){
                if (result == 1) new Alert(result, `Alcohol Volume updated to ${bottleSize}`);
                else new Alert(result, `An error occurred, please try again`);
            });
        }
    });
}


/*************
 * Prepares the product category options
 * builds a select of each category's attributes
 * Checks to see if attributes already exist for the product
 * and pre-fills them out
 ******************************************/
function prepareCategoryOptions() {
    // Product category select change
    $("#product-category").change(function(){
        // get and create the attribute list
        createAttributeList($(this).val()).then(function(result){
            $("#product-attribute-selection").html(result.html);

            // Check if product currently has attributes
            getProductAttributeData().then(function(result){
                if (result.result == 1) {
                    // If product has attributes attempt to set select values
                    for(let i = 0; i < result.product_attribute_data.length; i++) {
                        $(`[attribute-id='${result.product_attribute_data[i].attribute_id}']`).val(result.product_attribute_data[i].attribute_value_id);
                    }
                }
                // Make the select field searchable
                setTimeout(() => {$("[name='product-attribute']").selectize();}, 50);

            });
        });
    });
    $("#product-category option[value='-1']").remove();
    $("#product-category").val($("#current-product-category").val()).trigger("change");
    $("#product-category").selectize();

    $("#update-product-attributes").click(function(){
        if(confirm("Are you sure you want to change the product's category / attributes?")) {
            const categoryid = $("#product-category").val();
            const attributeIds = getProductAttributes();
            updateProductCategoryAndAttributes(categoryid, attributeIds).then(function(result){
                new Alert(result.result, result.message);
            });
        }
    });
}

function getProductAttributes() {
    // Product Attribute logic
    const productAttributes = $("[name=product-attribute]");
    const attributeValues = [];

    // Get attribute Ids and attributeValueIds
    productAttributes.each(function(){
        const attributeValueId = $(this).val();

        if (attributeValueId != -1) {
            attributeValues.push(attributeValueId);
        }
    });

    return attributeValues;
}


function prepareOverviewFunctionality() {
    // Loop through each overview update button
    $("[name='update-overview-section']").off();
    $("[name='update-overview-section']").each(function(){
        // Get section ID and current image src
        const overviewid = $(this).attr("overview-id");
        const currentImgSrc = $(`#current-product-overview-image-${overviewid}`).attr("src");

        // Enable previewing of uploaded image
        $(`#product-overview-image-${overviewid}`).off();
        $(`#product-overview-image-${overviewid}`).change(function(){
            const previewImg = $(this)[0].files[0];
            if (previewImg) {
                $(`#current-product-overview-image-${overviewid}`).attr("src", URL.createObjectURL(previewImg));
                console.log("changed");
            }
        });

        // Add click functionality
        $(this).off();
        $(this).click(function(){
            $(".form-feedback").remove();
            const title = $(`#product-overview-title-${overviewid}`).val();
            const textBody = $(`#product-overview-text-${overviewid}`).val();
            const image = $(`#product-overview-image-${overviewid}`)[0].files[0];
            
            // Validate fields
            let titleValid, textValid, imageValid = false;
            titleValid = checkFieldEmpty($(`#product-overview-title-${overviewid}`), "Please provide a title for this section", 255);
            textValid = checkFieldEmpty($(`#product-overview-text-${overviewid}`), "Please provide text for this section");
            if (image) {
                imageValid = checkFileField($(`#product-overview-image-${overviewid}`), null, ["png", "jpg", "jpeg", "webp"]);
            } else {
                imageValid = true;
            }
            
            // if fields valid update
            if (titleValid && textValid && imageValid) {
                updateProductOverviewSection(image, title, textBody, overviewid, currentImgSrc).then(function(result){
                    new Alert(result.result, result.message);
                })
            }
        }); 
    });

    // Loop through each remove overview button
    $("[name='remove-overview-section']").off();
    $("[name='remove-overview-section']").each(function(){
        // Add click functionality
        $(this).click(function(){
            if (!confirm("Are you sure you want to delete this section?")) return;
            const overviewid = $(this).attr("overview-id");

            deleteProductOverviewSection(overviewid).then(function(result){
                new Alert(result.result, result.message);
                if (result.result == 1) $(`#product-overview-item-${overviewid}`).remove();
            });
        })
    });

    $("#create-overview").off();
    $("#create-overview").click(function(){
        createNewProductOverviewSection();
    });
}


/***********
 * Creates a new product overview section and provides the functionality for upload.
 * Adds event listeners to the newly created section based on name.
 * Once it has been uploaded successfully the name tags are removed and the data attributes
 * required to update / remove it are applied with the ID of the newly created overview section
 */
function createNewProductOverviewSection() {
    // Guard clause to prevent multiple empty overviews from being created
    if (document.querySelector("[name='new-product-overview-item']")) return new Alert(false, "Please complete the current new overview section before creating another");

    // Create new overview item markup on page
    $("#create-overview").before("<div class='product-overview-item' name='new-product-overview-item'><div class='input-container-100'><label>Title</label><input type='text' class='form-item' name='new-product-overview-title'></div><div class='input-container-100'><label>Image <span class='optional'>-optional</span></label><input type='file' class='form-item' name='new-product-overview-image'><img src='' name='new-product-overview-preview-image' style='margin-top:20px;max-height:300px;'></div><div class='input-container-100'><label>Text content</label><textarea class='form-item' name='new-product-overview-text'></textarea></div><button name='remove-overview-section' id='remove-new-overview-section'>Remove</button><button name='update-overview-section' id='add-new-overview-section'>Save</button</div>")
    
    // add image preview functionality
    $("[name='new-product-overview-image']").change(function(){
        const image = $(this)[0].files[0];
        if (image) {
            $("[name='new-product-overview-preview-image']").attr("src", URL.createObjectURL(image));
        }
    });

    // Save button functionality
    $("#add-new-overview-section").click(function(){
        $(".form-feedback").remove();
        const title = $("[name='new-product-overview-title']").val();
        const text = $("[name='new-product-overview-text']").val();
        const image = $("[name='new-product-overview-image']")[0].files[0];

        // validate fields
        let titleValid, textValid, imageValid = false;
        titleValid = checkFieldEmpty($("[name='new-product-overview-title']"), "Please provide a title for this section", 255);
        textValid = checkFieldEmpty($("[name='new-product-overview-text']"), "Please provide text for this section");
        if (image) {
            imageValid = checkFileField($("[name='new-product-overview-image']"),null, ["png", "jpg", "jpeg", "webp"])
        } else {
            imageValid = true;
        }

        // All valid proceed to upload
        if (titleValid && textValid && imageValid) {
            uploadProductOverviewSection(image, title, text).then(function(result){
                new Alert(result.result, result.message);

                // If added successfully remove new attributes and replace with the retrieved ID
                if (result.result == 1) {
                    const id = result.id;
                    $("[name='new-product-overview-item']").attr("id", `product-overview-item-${id}`);
                    $("[name='new-product-overview-item']").attr("name", "");
                    $("[name='new-product-overview-title']").attr("id", `product-overview-title-${id}`);
                    $("[name='new-product-overview-title']").attr("name", "");
                    $("[name='new-product-overview-image']").attr("id", `product-overview-image-${id}`);
                    $("[name='new-product-overview-image']").attr("name", "");
                    $("[name='new-product-overview-preview-image']").attr("id", `current-product-overview-image-${id}`);
                    $("[name='new-product-overview-preview-image']").attr("name", "");
                    $("[name='new-product-overview-text']").attr("id", `product-overview-text-${id}`);
                    $("[name='new-product-overview-text']").attr("name", "");
                    $("#remove-new-overview-section").attr("overview-id", id);
                    $("#remove-new-overview-section").attr("id", "");
                    $("#add-new-overview-section").attr("overview-id", id);
                    $("#add-new-overview-section").text("Update");
                    $("#add-new-overview-section").attr("id", "");
                    prepareOverviewFunctionality();
                }
            });
        }
    });

    // Remove button functionality
    $("#remove-new-overview-section").click(function(){
        $("[name='new-product-overview-item']").remove();
    });
}


/*********************
 * AJAX FUNCTIONS
 *********************/
function updateProductName(name) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: {function: 5, productName: name, productid: productid}
        })
        .done(function(result){
            console.log(result);
            resolve(result);
        });
    });
}
function updateProductType(type) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: { function: 6, productType: type, productid: productid }
        })
        .done(function(result){
            resolve(result);
        });
    });
}
function updateProductPrice(price) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: { function: 7, productPrice: price, productid: productid }
        })
        .done(function(result){
            resolve(result);
        });
    });
}
function updateProductStock(stock) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: { function: 8, productStock: stock, productid: productid }
        })
        .done(function(result){
            resolve(result);
        });
    });
}
function updateProductDesc(desc) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: { function: 9, productDesc: desc, productid: productid }
        })
        .done(function(result){
            resolve(result);
        });
    });
}
function updateProductAlcVolume(alcVolume) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: { function: 10, alcVolume: alcVolume, productid: productid }
        })
        .done(function(result){
            resolve(result);
        });
    });
}
function updateProductBottleSize(bottleSize) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: { function: 11, bottleSize: bottleSize, productid: productid }
        })
        .done(function(result){
            console.log(result);
            resolve(result);
        });
    });
}

/***********************
 * Retrieves the markup for a category's attribute list via the handler file
 * Returns a promise with the following data:
 *  @result : boolean   |   determines whether a category has an attribute list
 *  @html : string      |   The markup for attribute <select>s
 ************************/
function createAttributeList() {
    return new Promise(function(resolve){
        const categoryid = $("#product-category").val();

        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: {function: 1, categoryid: categoryid}
        
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}


/***********
 * Retrieves the product's attribute data via the handler file
 * Returns a promise with the following data:
 *  @result : boolean   |   1 = product has attribute data  0 = no attribute data
 *  @product_attribute_data : array of objects |   an array of attribute_ids & attribute_value_ids
 *************************/
function getProductAttributeData() {
    return new Promise(function(resolve){
        const productid = $("#product-id").val();
        
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: {function: 2, productid: productid}
        
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}

function updateProductCategoryAndAttributes(categoryid, attributeValueIds) {
    return new Promise(function(resolve){
        const productid = $("#product-id").val();
        
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: {function: 12, productid: productid, categoryid: categoryid, attributeValueIds: attributeValueIds}
        
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}

function updateProductOverviewSection(image, title, text, overviewid, currentImgSrc) {
    return new Promise(function(resolve){
        let formData = new FormData();
        formData.append("function", 13);
        formData.append("overviewImage", image);
        formData.append("overviewTitle", title);
        formData.append("overviewText", text);
        formData.append("overviewid", overviewid);
        formData.append("productid", $("#product-id").val());
        formData.append("currentImgSrc", currentImgSrc);

        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false
        
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}

function deleteProductOverviewSection(overviewid) {
    return new Promise(function(resolve){
        const productid = $("#product-id").val();
        
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: {function: 14, overviewid: overviewid, productid: productid}
        
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}

function uploadProductOverviewSection(image, title, text) {
    return new Promise(function(resolve){
        let formData = new FormData();
        formData.append("function", 15);
        formData.append("overviewImage", image);
        formData.append("overviewTitle", title);
        formData.append("overviewText", text);
        formData.append("productid", $("#product-id").val());
        
        $.ajax({
            url: "../php/ajax-handlers/product-edit-handler.php",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false
        
        })
        .done(function(result){
            console.log(result);
            resolve(JSON.parse(result));
        });
    });
}