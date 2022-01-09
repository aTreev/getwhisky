function prepareProductCreationPage() {

    // Apply logic to the category select
    $("#product-category").selectize();
    $("#product-category").change(function(){
        // Implement selectize library to allow searching through select
        setTimeout(() => {$("[name='product-attribute']").selectize();}, 50);

        const categoryid = $(this).val();
        getAttributeList(categoryid).then(function(result){
            $("#product-attribute-selection").html(result.html);
        });
    });


    // Create a preview image when input[file] changes
    $("#product-image").change(function(){
        const image = $(this)[0].files[0];
        if (image) {
            $("#product-image-preview").attr("src", URL.createObjectURL(image));
        }
    });


    // Save button majority of page logic here
    $("#save-product").click(function(){
        validateAndCreateProduct();
    });

    // Button to create new overview section
    $("#create-overview").click(function(){
        createNewProductOverviewSection();
    })
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

/***************
 * Creates the markup for a product overview section with all
 * the required identifiers.
 * Also applies removal and preview logic
 ****************************/
function createNewProductOverviewSection() {
    $("#create-overview").before(`<div class='product-overview-item' name='product-overview-item'><div class='input-container-100'><label>Title</label><input type='text' class='form-item' name='product-overview-title'></div><div class='input-container-100'><label>Image <span class='optional'>-optional</span></label><input type='file' class='form-item' name='product-overview-image'><img src='' style='margin-top:20px;max-height:300px;'></div><div class='input-container-100'><label>Text content</label><textarea class='form-item' name='product-overview-text'></textarea></div><button name='remove-overview-section'>Remove</button</div>`)
    // Adds image preview functionality
    $("[name='product-overview-image']").change(function(){
        if(!$(this)[0].files[0]) return;
        $(this).next().attr("src", URL.createObjectURL($(this)[0].files[0]));
    });
    // Adds removal functionality
    $("[name='remove-overview-section']").click(function(){ $(this).parent().remove(); });
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



/******************
 * Gets and validates each product overview from the page
 * returns a validity flag along with the overview details
 * @return Object
 *      @valid : boolean (true if all valid)
 *      @hasContent : boolean (true if overviews have been created)
 *      @overviewImages : array - File or 0 to indicate no file
 *      @overviewTitles : Text array
 *      @overviewTexts : Text array
 *      @overviewPostIndex : array - An index of files, 1 if file else 0
 *****************************************************/
function getProductOverviews() {
    let productOverviews = {
        valid: false,
        hasContent: false,
        overviewImages: [],
        overviewTitles: [],
        overviewTexts: [],
        overviewPostIndex: []
    }
    let allValid = true;

    // return a valid response by default if no product overview items
    if (!$("[name='product-overview-item']")) return productOverviews.valid = true;

    // Iterate through each product overview item and validate
    const overviewImage = $("[name='product-overview-image']");
    const overviewTitle = $("[name='product-overview-title']");
    const overviewText = $("[name='product-overview-text']");
    
    overviewImage.each(function(){
        // If file selected validate
        if($(this)[0].files[0]) {
            if (!checkFileField($(this), null, ["png", "jpg", "jpeg", "webp"])) return allValid = false;
            productOverviews.hasContent = true;
            productOverviews.overviewImages.push($(this)[0].files[0]);
            productOverviews.overviewPostIndex.push(1);
        } else {
            // add 0 to indicate no file at this position
            productOverviews.overviewPostIndex.push(0);
        }
        
    });

    overviewTitle.each(function(){
        if (!checkFieldEmpty($(this),"Please provide a title for this section", 255)) return allValid = false;
        productOverviews.hasContent = true;
        productOverviews.overviewTitles.push($(this).val());
    });

    overviewText.each(function(){
        if (!checkFieldEmpty($(this), "Please provide text content for this section")) return allValid = false;
        productOverviews.hasContent = true;
        productOverviews.overviewTexts.push($(this).val());
    });

    // If all still valid change boolean
    if (allValid) productOverviews.valid = true;
    return productOverviews;
}



function validateAndCreateProduct() {
    $(".form-feedback").remove();
        // Primary details
        const nameField = $("#product-name");
        const typeField = $("#product-type");
        const priceField = $("#product-price");
        const stockField = $("#product-stock");
        const descField = $("#product-desc");
        const imageField = $("#product-image");
        const categorySelectField = $("#product-category");

        // Secondary details
        const alcVolField = $("#product-alc-volume");
        const bottleSizeField = $("#product-bottle-size");
        // Convert overviews to sendable POST data
        const productOverviews = getProductOverviews();
        const attributeValues = getProductAttributes();

        // Product required detail validation
        let nameValid, typeValid, priceValid, stockValid, descValid, imageValid, categorySelected, overviewsValid = false;

        nameValid = checkFieldEmpty(nameField, "Please provide a product name", 90);
        typeValid = checkFieldEmpty(typeField, "Please enter a product type", 30);
        priceValid = checkNumberField(priceField, "Please provide a price", [0, 99999])
        stockValid = checkNumberField(stockField, "Please enter an initial stock value", [-1, null]);
        descValid = checkFieldEmpty(descField, "Please provide a product description");
        imageValid = checkFileField(imageField, "Please provide a product image", ["png", "jpg", "jpeg", "webp"])
        categorySelected = checkSelectField(categorySelectField, "Please choose a product category");
        overviewsValid = productOverviews.valid;

        // All required fields present & valid
        if (nameValid && typeValid && priceValid && stockValid && descValid && imageValid && categorySelected && overviewsValid) {
            // Confirm product creation without filters
            if (attributeValues.length == 0) {
                // No filters selected confirm upload
                if (!confirm("are you sure you want to create a product without attributes? (it wont appear on any product filters)")) return;

                uploadProduct(nameField.val(), typeField.val(), priceField.val(), stockField.val(), descField.val(), imageField[0].files[0], alcVolField.val(), bottleSizeField.val(), categorySelectField.val(), attributeValues, productOverviews)
            } else {
                // Upload product
                uploadProduct(nameField.val(), typeField.val(), priceField.val(), stockField.val(), descField.val(), imageField[0].files[0], alcVolField.val(), bottleSizeField.val(), categorySelectField.val(), attributeValues, productOverviews)
            }
        }
}

function uploadProduct(name, type, price, stock, desc, image, alcoholVolume, bottleSize, categoryid, attributeValueIds, productOverviews) {
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
    formData.append("attributeValueIds", JSON.stringify(attributeValueIds));
    formData.append("overviewPostIndex", JSON.stringify(productOverviews.overviewPostIndex));
    formData.append("overviewTitles", JSON.stringify(productOverviews.overviewTitles));
    formData.append("overviewTexts", JSON.stringify(productOverviews.overviewTexts));
    for(let i = 0; i < productOverviews.overviewImages.length; i++) {
    formData.append("overviewImages[]", productOverviews.overviewImages[i]);
    }
    
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
        console.log(result);
        new Alert(result.result, result.message);
    });
}