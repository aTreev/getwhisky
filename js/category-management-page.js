
function prepareCategoryManagementPage() {
    const categorySelect = $("#product-category");

    categorySelect.change(function(){
        if ($(this).val() == -1) return;
        const categoryid = $(this).val();
        getAttributeListForManagement(categoryid).then(function(result){
            $(".category-attributes").html(result.html);
            $(".category-attributes").css({"display": "block"});
            prepareAttributeFunctionality(categoryid, categorySelect);
            prepareAttributeValueFunctionality(categoryid, categorySelect);
        });
    });
}

function prepareAttributeFunctionality(categoryid, categorySelect) {
    $("#new-attribute").click(function(){
        // add text input and button to save
        $(".category-options").html("<div class='new-attribute-container'><input type='text' class='form-item' id='new-attribute-input'><button class='save-btn' id='save-new-attribute'>save</button></div>");

        // add logic to save button
        $("#new-attribute-input").focus();
        $("#new-attribute-input").keyup(function(e){ if(e.keyCode === 13) $("#save-new-attribute").click(); });
        $("#save-new-attribute").click(function(){
            $(".form-feedback").remove();
            const newAttributeTitle = $("#new-attribute-input").val();

            // Validate attribute title
            let attrTitleValid = checkFieldEmpty($("#new-attribute-input"), "Please provide a title for the attribute", 50);

            if (attrTitleValid && confirm("are you sure you want to add a new attribute? This will appear as a new product filter option")) {   
                // User accepts, upload
                uploadNewCategoryAttribute(categoryid, newAttributeTitle).then(function(result){
                    new Alert(result.result, result.message);

                    if (result.result == 1) {
                        // Force select change on success to reload html
                        categorySelect.val(categoryid.toString());
                        categorySelect.trigger("change");
                    }
                });
            }
        });
    });


    // Remove attribute button logic
    $("[name='remove-attribute']").each(function(){

        $(this).click(function(){
            const attributeid = $(this).attr("attribute-id");
            if (confirm("WARNING: Deleting an attribute removes it from the product filter list and deletes all associated attribute values. Are you sure you want to delete this attribute? (This action cannot be reversed)")) {
                deleteCategoryAttribute(categoryid, attributeid).then(function(result){
                    new Alert(result.result, result.message);
                    if (result.result == 1) {
                        // Force select change on success to reload html
                        categorySelect.val(categoryid.toString());
                        categorySelect.trigger("change");
                    }
                });
            }
        });
    }); 

    // CSS toggle for the attribute management
    $("[name='manage-attribute']").each(function(){
        $(this).click(function(){
            const attributeid = $(this).attr("attribute-id");
            $(`.attribute-item-values[attribute-id='${attributeid}']`).toggle();

            if ($(`.attribute-item-values[attribute-id='${attributeid}']`).css("display") == "block") {
                $(this).text("Close filter values");
            } 
            if ($(`.attribute-item-values[attribute-id='${attributeid}']`).css("display") == "none") {
                $(this).text("Manage filter values");
            }
        })
    });

}

function prepareAttributeValueFunctionality(categoryid, categorySelect) {
    // remove attribute value button
    $("[name='remove-attribute-val']").each(function(){
        $(this).click(function(){
            const attributeid = $(this).attr("attribute-id");
            const attributeValueId = $(this).attr("attribute-value-id");

            if (confirm("WARNING: This will remove this filter option for all products. Are you sure you want to delete this filter option? (This action cannot be reversed)")) {
                deleteAttributeValue(attributeid, attributeValueId).then(function(result){
                    new Alert(result.result, result.message);
                    if (result.result == 1) {
                        // Force select change on success to reload html
                        categorySelect.val(categoryid.toString());
                        categorySelect.trigger("change");
                        setTimeout(() => {$(`.attribute-item-values[attribute-id='${attributeid}']`).show();}, 50);
                    }
                })
            }
        });
    });

    // add new attribute value button
    $("[name='add-new-attribute-value']").each(function(){
        $(this).click(function(){
            const attributeid = $(this).attr("attribute-id");
            $(`.filter-options[attribute-id='${attributeid}']`).html(`<div class='new-attribute-container'><input type='text' class='form-item' id='new-attribute-value-input-${attributeid}'><button class='save-btn' id='save-new-attribute-value-${attributeid}'>save</button></div>`);
            
            // Add click logic to save new attribute value button
            $(`#new-attribute-value-input-${attributeid}`).focus();
            $(`#new-attribute-value-input-${attributeid}`).keyup(function(e) {if (e.keyCode === 13) $(`#save-new-attribute-value-${attributeid}`).click();});
            $(`#save-new-attribute-value-${attributeid}`).click(function(){
                const attributeValue = $(`#new-attribute-value-input-${attributeid}`).val();

                // validate
                const attrValueValid = checkFieldEmpty($(`#new-attribute-value-input-${attributeid}`), "Please enter a name for the filter option", 200);

                if (attrValueValid) {
                    uploadAttributeValue(attributeid, attributeValue).then(function(result){
                        new Alert(result.result, result.message);
                        if (result.result == 1) {
                            // Force select change on success to reload html
                            categorySelect.val(categoryid.toString());
                            categorySelect.trigger("change");
                            setTimeout(() => {$(`.attribute-item-values[attribute-id='${attributeid}']`).show();}, 50);
                        }
                    });
                }
            });
        })
    });
}



function getAttributeListForManagement(categoryid) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/category-management-handler.php",
            method: "POST",
            data: { function: 1, categoryid: categoryid }
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}

function uploadNewCategoryAttribute(categoryid, newAttributeTitle) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/category-management-handler.php",
            method: "POST",
            data: {function: 2, categoryid: categoryid, newAttributeTitle: newAttributeTitle}
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}   

function deleteCategoryAttribute(categoryid, attributeid) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/category-management-handler.php",
            method: "POST",
            data: {function: 3, categoryid: categoryid, attributeid: attributeid}
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}

function deleteAttributeValue(attributeid, attributeValueId) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/category-management-handler.php",
            method: "POST",
            data: {function: 4, attributeid: attributeid, attributeValueId: attributeValueId}
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}

function uploadAttributeValue(attributeid, attributeValue) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/category-management-handler.php",
            method: "POST",
            data: {function: 5, attributeid: attributeid, attributeValue: attributeValue}
        })
        .done(function(result){
            console.log(result);
            resolve(JSON.parse(result));
        });
    });
}