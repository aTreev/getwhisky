/*****************
 * File functions similarly to the form-functions file except these functions
 * are specific to addresses
 *****************************************/

function prepareUserAddressPage() {
    $("#address-root").show();
    $("#add-new-address-btn").click(function(){
        $(".form-feedback").remove();
        $("input[name=add-address]").val("");
        showModal("add-address-modal", true);
    });

    prepareNewAddressForm();
    prepareEditAddressForms();
    prepareDeleteAddressFunctionality();

    // if on delivery page call this function
    if(window.location.href.split("/").pop() == "deliveryselection.php") prepareDeliveryPage();
}


function prepareNewAddressForm() {
    const identifierField = $("#add-identifier");
    const fullNameField = $("#add-full-name");
    const mobileField = $("#add-mobile");
    const postcodeField = $("#add-postcode");
    const line1Field = $("#add-line1");
    const line2Field = $("#add-line2");
    const cityField = $("#add-city");
    const countyField = $("#add-county");

    // Bug caused event listeners to be applied multiple times
    // even though the entire page gets reloaded. this seems to work
    $("#new-address-submit").off();
    $("#new-address-submit").on("click",function(ev){
        ev.preventDefault();
        $(".form-feedback").remove();
        let id, nm, mb, pc, l1, ci = false;
        id = checkAddressFieldValid(identifierField, 50, "Please enter a name for the address");
        nm = checkAddressFieldValid(fullNameField, 90, "Please enter the recipients full name");
        mb = checkAddressMobile(mobileField);
        pc = checkAddressPostcode(postcodeField);
        l1 = checkAddressFieldValid(line1Field, 80, "Please enter the first line of the address");
        ci = checkAddressFieldValid(cityField, 50, "Please enter a UK town or city");

        if (id && nm && mb && pc && l1 && ci) {
            // All required fields valid
            addUserAddress(identifierField.val(), fullNameField.val(), mobileField.val(), postcodeField.val(), line1Field.val(), line2Field.val(), cityField.val(), countyField.val());
            hideModal("add-address-modal");
        }

    });
}

/***********
 * Provides edit address functionality to the edit address forms
 * on the address page.
 * This layout should make it impossible for folk to manipulate address_ids
 * as they are retrieved on page load.
 * Even if they could the validation on the backend wouldn't allow for anything
 * malicious anyway
 */
function prepareEditAddressForms() {
    const addressItems = $(".address-item");

    // Foreach address-item add the edit functionality
    addressItems.each(function(){
        const addressId = $(this).attr("id");
        
        // Toggle the edit form on edit-button click
        $("#edit-"+addressId).click(function(){
            $("#address-item-edit-form-"+addressId).toggleClass("address-item-edit-form-show");
        });

        // Validate and update address on edit form submit
        // Follows same format as the add address function
        $("#edit-submit-"+addressId).click(function(ev){
            ev.preventDefault();
            const identifierField = $("#edit-identifier-"+addressId);
            const fullNameField = $("#edit-full-name-"+addressId);
            const mobileField = $("#edit-mobile-"+addressId);
            const postcodeField = $("#edit-postcode-"+addressId);
            const line1Field = $("#edit-line1-"+addressId);
            const line2Field = $("#edit-line2-"+addressId);
            const cityField = $("#edit-city-"+addressId);
            const countyField = $("#edit-county-"+addressId);

            $(".form-feedback").remove();
            let id, nm, mb, pc, l1, ci = false;
            id = checkAddressFieldValid(identifierField, 50, "Please enter a name for the address");
            nm = checkAddressFieldValid(fullNameField, 90, "Please enter the recipients full name");
            mb = checkAddressMobile(mobileField);
            pc = checkAddressPostcode(postcodeField);
            l1 = checkAddressFieldValid(line1Field, 80, "Please enter the first line of the address");
            ci = checkAddressFieldValid(cityField, 50, "Please enter a UK town or city");

            if (id && nm && mb && pc && l1 && ci) {
                $("#address-item-edit-form-"+addressId).toggleClass("address-item-edit-form-show");
                updateUserAddress(addressId, identifierField.val(), fullNameField.val(), mobileField.val(), postcodeField.val(), line1Field.val(), line2Field.val(), cityField.val(), countyField.val());
            }
        });
    })
}

function prepareDeleteAddressFunctionality() {
    const addressItems = $(".address-item");

    addressItems.each(function(){
        const addressId = $(this).attr("id");

        $("#delete-"+addressId).click(function(){
            const identifier = $("#edit-identifier-"+addressId).val();
            if (!confirm(`Are you sure you wish to delete address ${identifier}?`)) return;

            deleteUserAddress(addressId, identifier);
        });
    })
}

/********
 * Adds the ability to select an address
 * on the delivery page only
 * works by adding address id to an invisible form field
 * on the delivery page
 * additionally validates an email address when a guest user
 * is present
 ********************************/
function prepareDeliveryPage() {
    const addressItems = $(".address-item");
    $("[name='addressId']").val("");

    // Loop through each address item
    addressItems.each(function(){
        // Get the id of the address
        const addressId = $(this).attr("id");
        $(this).hover(function(){$(this).css("cursor","pointer")});

        // When address clicked transfer the ID to the hidden form and change CSS
        $(this).click(function(){
            addressItems.css({"background-color":"white", "border-color":"lightgrey"});
            $(this).css({"background-color":"lightgrey", "border-color":"black"});
            $("[name='addressId']").val(addressId);
        });
    });

    // Check for required fields
    $("#delivery-submit").click(function(ev){
        $(".form-feedback").remove();
        // Hidden addressId input
        if ($("[name='addressId']").val() == "") {
            ev.preventDefault();
            new Alert(false, "Please select a delivery address");
        }
        // User email input
        if (!checkEmail($("#user-email"))) {
            ev.preventDefault();
        }
    });
}

// Generic valdiation for an input field
// Takes the input field max length of the field 
// and a feedback message as for an empty field
function checkAddressFieldValid(inputField, maxLength, feedBackMessage) {
    const value = inputField.val();
    
    if (value.length <= 0) {
        doFeedback(inputField, feedBackMessage);
        return false;
    }

    if (value.length > maxLength) {
        doFeedback(inputField, `Please enter a value lower than ${maxLength}`);
        return false;
    }
    return true;
}

// Checks that a UK telephone number is valid
function checkAddressMobile(mobileField) {
    const mobile = mobileField.val();
    const re = new RegExp(/^(?:(?:\(?(?:0(?:0|11)\)?[\s-]?\(?|\+)44\)?[\s-]?(?:\(?0\)?[\s-]?)?)|(?:\(?0))(?:(?:\d{5}\)?[\s-]?\d{4,5})|(?:\d{4}\)?[\s-]?(?:\d{5}|\d{3}[\s-]?\d{3}))|(?:\d{3}\)?[\s-]?\d{3}[\s-]?\d{3,4})|(?:\d{2}\)?[\s-]?\d{4}[\s-]?\d{4}))(?:[\s-]?(?:x|ext\.?|\#)\d{3,4})?$/);

    if ((mobile.length > 0 && !re.test(mobile))) {
        doFeedback(mobileField, "Please enter a valid UK phone number");
        return false;
    }
    return true;
}


// Checks that a UK postcode is valid
function checkAddressPostcode(postcodeField) {
    const postcode = postcodeField.val();
    const re = new RegExp(/([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9][A-Za-z]?))))\s?[0-9][A-Za-z]{2})/);

    if (!re.test(postcode)) {
        doFeedback(postcodeField, "Please enter a valid UK postcode");
        return false;
    }
    return true;
}


/**********
 * Adds a user's address to the database
 * reloads HTML on complete
 *********************************/
function addUserAddress(identifier, fullName, mobile, postcode, line1, line2, city, county) {
    $.ajax({
        url:"../php/ajax-handlers/user-address-handler.php",
        method: "POST",
        data: {function: 1, identifier: identifier, fullName: fullName, phoneNumber: mobile, postcode: postcode, line1: line1, line2: line2, city: city, county: county}
    })
    .done(function(result) {
        console.log(result);
        result = JSON.parse(result);

        if (result.result == 1) {
            $("#address-root").html(result.html);
            new Alert(true, `Address '${identifier}' added successfully`);
            // reload event listeners to new html
            prepareUserAddressPage();

        } else {
            new Alert(false, "Failed to add address, please refresh and try again");
        }
    });
}


/*******************
 * Updates a user's address on the database
 * reloads HTML on complete
 **************************/
function updateUserAddress(addressId, identifier, fullName, mobile, postcode, line1, line2, city, county) {
    $.ajax({
        url:"../php/ajax-handlers/user-address-handler.php",
        method: "POST",
        data: {function: 2, addressId: addressId, identifier: identifier, fullName: fullName, phoneNumber: mobile, postcode: postcode, line1: line1, line2: line2, city: city, county: county}
    })
    .done(function(result) {
        result = JSON.parse(result);

        if (result.result == 1) {
            // reload event listeners to new html
            setTimeout(() => {
                new Alert(true, `Address '${identifier}' updated successfully`);
                $("#address-root").html(result.html);
                prepareUserAddressPage();
            }, 500);
        } else {
            new Alert(false, "Failed to update address, please refresh and try again");
        }
    });
}


/******************
 * Deletes a user's address from the database
 * reloads HTML on complete
 *************************/
function deleteUserAddress(addressId, identifier) {
    $.ajax({
        url:"../php/ajax-handlers/user-address-handler.php",
        method: "POST",
        data: {function: 3, addressId: addressId}
    })
    .done(function(result) {
        result = JSON.parse(result);

        if (result.result == 1) {
            new Alert(true, `Address '${identifier}' deleted successfully`);
            $("#address-root").html(result.html);
            prepareUserAddressPage();
        } else {
            new Alert(false, "Failed to delete address, please refresh and try again");
        }
    });
}