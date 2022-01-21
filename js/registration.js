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
    console.log(window.location.pathname);
    
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
            if (window.location.pathname == "/checkout-sign-up.php") {
                // Registered at checkout redirect to next step
                window.location.href = "/checkout.php";
            } else {
                // Registered from registration page do a fancy animation on success
                form.css("height", "510px");
                form.html(`<div class='register-thank-you'><h3>Thank you!</h3><p>You have successfully registered to getwhisky.</p><p>A verification email has been sent to ${email}</p><a href='user.php'>Take me to my account</a></div>`);
                setTimeout(() => {
                    $(".register-thank-you").addClass("register-thank-you-show");
                }, 200);
            }
            
    
		}
	})
}
