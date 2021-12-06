/*****************
 * This file contains form validation functions and a method of feedback that in theory should be reusable.
 * Also contains one time use functions specific to certain forms.
 * Requires a handler function to retrieve form elements which are passed to the validation functions
 *****************************************************/
function prepareRegistrationForm() {
	const form = $("#regform");
	const firstNameField = $("#firstname");
	const surnameField = $("#surname");
	const emailField = $("#email");
	const passwordField = $("#userpass");
	const repeatPasswordField = $("#repeatUserpass");
	const submitButton = $("#submitbutton");

	// Provide interactive password feedback
	passwordField.keyup(function(){
		testPasswordStrength(passwordField);
	});

	/*********
	 * Check that all fields are valid before registering user
	 ***************************/
	submitButton.click(function(ev){
		$(".form-feedback").remove();
		ev.preventDefault();
		let em = fn = sn = pw = false;

		// Promise setup due to having to wait for an AJAX call
		registrationCheckEmail(emailField).then(function(valid){
			em = valid;
			sn = checkName(surnameField);
			fn = checkName(firstNameField);
			pw = checkPassword(passwordField, repeatPasswordField);

			// register the user
			if (em && fn && sn && pw) {
				submitButton.html("Please wait...");
				registerUser(form, firstNameField.val(), surnameField.val(), emailField.val(), passwordField.val())
			}
		});
	});
}

function doFeedback(inputField, feedbackStr) {
	inputField.parent().after("<div class='form-feedback'><i class='fas fa-exclamation-circle'></i><p>"+feedbackStr+"</p></div>");
}


// Validates a username & email address
// Checks to see if the inputs are valid
// Checks to see if they already exist on the database
// returns a promise boolean valid or !valid
function registrationCheckEmail(emailField) {
	const email = emailField.val();

	return new Promise(function(resolve) {
		$.ajax({
			// Ajax parameters
			url:"../php/ajax-handlers/checkuser.php",
			method:"POST",
			data: {email: email}
			
		})
		.done(function(result){
			result = JSON.parse(result);
			let valid = true;

			if (result.emailexists == 1) {
				doFeedback(emailField, "Email already exists"); valid = false;
			}

			if (result.emailexists == 2) {
				doFeedback(emailField, "Invalid email format"); valid = false;
			}
			// Return valid as promise callback
			resolve(valid);
		});
	});
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
    if(passwordStrength>8) { feedbackcolour="#FF0"; }
    // Green colour for strong passwords
    if(passwordStrength>16) { feedbackcolour="#0F0"; }
    // Red for weak
    if(passwordStrength<8) { feedbackcolour="#F00";}

    var fbtarget=document.getElementById("password-feedback");
    if(!fbtarget && password.length > 0) {
        var fb=document.createElement("p");
        fb.setAttribute("id", "password-feedback");
        fb.classList.add("password-indicator");
        passwordField.parent().after(fb);
        fbtarget=document.getElementById("password-feedback");
        fbtarget.style.display="block";
        fbtarget.style.height="1em";
    }
    fbtarget.style.backgroundColor=feedbackcolour;
    fbtarget.style.width=passwordStrength+"em"

    return passwordStrength;
}


/****************
 * AJAX function that registers the user when all required parameters are sent
 * Performs additional validation through PHP on the backend
 * Displays the response as a message
 * TODO: add fail message
 ***************************************/
function registerUser(form, firstName, surname, email, password) {
	$.ajax({
		url: "../php/ajax-handlers/user-reg-handler.php",
		method: "POST",
		data: {firstname:firstName, surname:surname, email:email, userpass:password}
	})
	.done(function(result){
		console.log(result);
		result = JSON.parse(result);

		if (result.insert == 1) {
			// do a fancy animation on success
			form.css("height", "510px");
			form.html(`<div class='register-thank-you'><h3>Thank you!</h3><p>You have successfully registered to getwhisky.</p><p>A verification email has been sent to ${email}</p><a href='user.php'>Take me to my account</a></div>`);
			setTimeout(() => {
				$(".register-thank-you").addClass("register-thank-you-show");
			}, 200);
		}
	})
}



