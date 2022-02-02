function doFeedback(inputField, feedbackStr) {
	inputField.parent().after("<div class='form-feedback'><i class='fas fa-exclamation-circle'></i><p>"+feedbackStr+"</p></div>");
	inputField.get(0).scrollIntoView({block: "center"});

}




/***********************
 * Checks that an email address is valid
 * by testing it against a common email RegEx
 ***********************************/
function checkEmail(emailField) {
	const email = emailField.val();
	const re = new RegExp(/(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/);

	if (email.length <= 0 || !re.test(email)) {
		doFeedback(emailField, "Please enter a valid email address");
		return false;
	}
	return true;
}


function checkName(nameField) {
	const name = nameField.val();
	const nameType = nameField.attr("name");
	let valid = true;

	if (name.length == 0) {
		doFeedback(nameField, `Please enter a ${nameType}`);
		valid = false;
	}
	if (name.length > 30) {
		doFeedback(nameField, `${nameType} must be less than 30 characters`);
		valid = false;
	}
	return valid;
}


function checkPassword(passwordField, repeatPasswordField) {
	const password = passwordField.val();
	const repeatPass = repeatPasswordField.val();

	if (password.length == 0) {
		doFeedback(repeatPasswordField, "Please enter a password"); 
		return false;
	}
	if (testPasswordStrength(passwordField) < 8) {
		doFeedback(repeatPasswordField, "Password does not meet minimum complexity"); 
		return false;
	}
	if (password != repeatPass) {
		doFeedback(repeatPasswordField, "Passwords must match"); 
		return false;
	}
	
	return true;
}

/********
 * Generic validation for an input field, checks that an input is not empty
 * Takes a feedback message to display a specific message on condition fail
 * Option max length check to ensure a max length is not hit
 *******************************************/
function checkFieldEmpty(field, feedbackMessage, maxLen) {
	const fieldValue = field.val();

	if (fieldValue.length == 0) {
		doFeedback(field, feedbackMessage);
		return false;
	}
	if (maxLen && fieldValue.length > maxLen) {
		doFeedback(field, `Value must be less than ${maxLen} characters`);
		return false;
	}
	return true;
}

/*****
 * Function checks to see if a file has been uploaded,
 * Takes a feedback message to provide a specific message on condition fail
 * Additionally takes an array of allowed file types to ensure only the correct
 * file types are provided
 ****************************************/
function checkFileField(fileField, feedbackMessage, allowedFileTypes) {
	const file = fileField[0].files[0];

	if (feedbackMessage != null && !file) {
		doFeedback(fileField, feedbackMessage);
		return false;
	}

	if (file && allowedFileTypes) {
		const fileExt = file.type.split("/").pop();

		if (!allowedFileTypes.includes(fileExt)) {
			doFeedback(fileField, "File must be of one of the types ( "+ allowedFileTypes +" )");
			return false;
		}
	}
	return true;
}

/*************
 * Validates a datetime input type to ensure
 * it isn't null and also to ensure that it is
 * a date in the future
 **************************/
function checkDatetimeField(datetimeField) {
	const datetime = datetimeField.val();
	const now = new Date().toISOString().split("Z")[0];

	if (datetime.length == 0) {
		doFeedback(datetimeField, "Please insert a date and time");
		return false;
	}
	if (datetime < now) {
		doFeedback(datetimeField, "Please select a time in the future");
		return false;
	}

	return true;
}

/*****
 * Validates a number Int or Float
 * Takes a feedback message to provide on empty value
 * Optional minmax array allows for setting value bounds
 ********************/
function checkNumberField(numberField, feedbackMessage, minMax = [null, null]) {
	const number = numberField.val();
	const minVal = minMax[0];
	const maxVal = minMax[1]; 
	if (number.length <= 0) {
		doFeedback(numberField, feedbackMessage);
		return false;
	}

	if (isNaN(number)) {
		doFeedback(numberField, "Please provide a number");
		return false;
	}

	if (minVal != null && number <= minVal) {
		doFeedback(numberField, `Please enter a number greater than ${minVal}`);
		return false;
	}

	if (maxVal != null && number >= maxVal) {
		doFeedback(numberField, `Please enter a number lower than ${maxVal}`);
		return false;
	}

	return true;
}


function checkSelectField(selectField, feedbackStr) {
	const selectValue = selectField.val();

	if (selectValue == -1) {
		doFeedback(selectField, feedbackStr);
		return false;
	}
	return true;
}

/**************************
 * Tests a password against regex to ensure that
 * a minimum complexity is met, takes in the password
 * input element as an argument.
 * Appends a password strength indicator to the password input
 * returns the password strength as float.
 ************************************************************/
 function testPasswordStrength(passwordField) {
    let password = passwordField.val();
    let feedbackcolour;
	let feedbackText;
	let textColour;
    // Get the total length of password, baseline strength of 1 for a 6 character password
    let passtr=(password.length<6)?1:password.length/2;
    // Use Regex to find what types of characters, symbols and numbers used
    let hassymbol=((/[-!£$%^&*()_+|~=`{}\[\]:";'<>?,.\/]/).test(password))?1.5:0;
    let hasnumeric=((/[0-9]/).test(password))?1.4:0;
    let hasupper=((/[A-Z]/).test(password))?1.2:0;
    let haslower=((/[a-z]/).test(password))?1.1:0;
    // Calculate the overall relative strength of the password
    passwordStrength=passtr*(hassymbol+hasnumeric+hasupper+haslower);
    
	// Cap for strong passwords
    if(passwordStrength>24) { passwordStrength=24; }
    // Yellow colour for medium strength passwords
    if(passwordStrength>8) { 
		feedbackcolour="#ffff8c"; 
		textColour="#929502";
		feedbackText="Good";
	}
    // Green colour for strong passwords
    if(passwordStrength>16) { 
		feedbackcolour="lightgreen"; 
		textColour="darkgreen";
		feedbackText = "Strong";
	}
    // Red for weak
    if(passwordStrength<8) { 
		feedbackcolour="rgb(255, 110, 110)";
		textColour = "darkred";
		feedbackText="Weak";
	}

	$("#password-feedback").remove();
    if(password.length > 0) {
        passwordField.parent().after(`<div id='password-feedback' style='margin-top:-15px;margin-bottom:10px;padding: 10px;background-color:${feedbackcolour};display:flex;align-items:center;'><p style='color:${textColour};font-weight:600;'>${feedbackText}</p></div>`);
    }
    return passwordStrength;
}


