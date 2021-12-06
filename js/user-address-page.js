/*****************
 * File functions similarly to the form-functions file except these functions
 * are specific to addresses
 *****************************************/
function prepareUserAddressPage() {
    $("#add-new-address-btn").click(function(){
        $(".form-feedback").remove();
        $("input[name=add-address]").val("");
        showModal("add-address-modal", true);
    });

    prepareNewAddressForm();
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

    $("#new-address-submit").click(function(ev){
        ev.preventDefault();
        $(".form-feedback").remove();
        let id, nm, mb, pc, l1, ci, co = false;
        id = checkAddressFieldValid(identifierField, 50, "Please enter a name for the address");
        nm = checkAddressFieldValid(fullNameField, 90, "Please enter the recipients full name");
        mb = checkAddressMobile(mobileField);
        pc = checkAddressPostcode(postcodeField);
        l1 = checkAddressFieldValid(line1Field, 80, "Please enter the first line of the address");
        ci = checkAddressFieldValid(cityField, 50, "Please enter a UK town or city");

        if (id && nm && mb && pc && l1 && ci) {
            // All required fields valid
            hideModal("add-address-modal");
            addUserAddress(identifierField.val(), fullNameField.val(), mobileField.val(), postcodeField.val(), line1Field.val(), line2Field.val(), cityField.val(), countyField.val());
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

function addUserAddress(identifier, fullName, mobile, postcode, line1, line2, city, county) {
    $.ajax({
        url:"../php/ajax-handlers/user-address-handler.php",
        method: "POST",
        data: {function: 1, identifier: identifier, fullName: fullName, phoneNumber: mobile, postcode: postcode, line1: line1, line2: line2, city: city, county: county}
    })
    .done(function(result) {
        console.log(result);
    });
}