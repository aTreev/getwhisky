function prepareLoginPage() {
    $("#forgot-password").click(function(){
        showModal("password-reset-modal",true);
    });

    $("#close-password-reset-modal").click(function(){
        hideModal("password-reset-modal");
    });
    
    $("#submit-password-reset").click(function(e){
        e.preventDefault();
    if ($("#password-reset-email-input").val()) {
        validateResetEmail($("#password-reset-email-input").val());
    }
    });


    // Login form submit handle login
    $("#login-form").submit(function(ev){
        // prevent default & remove feedback
        ev.preventDefault();
        $(".form-feedback").remove();
        
        // Get login details
        const emailField = $("#login-email");
        const passwordField = $("#login-password");
        // Check for login from checkout page
        const checkoutPage = $("#checkout").val() || 0;

        // Check if fields are empty
        let em, pw = false;

        em = checkFieldEmpty(emailField, "Please enter your email address");
        pw = checkFieldEmpty(passwordField, "Please enter your password");

        // Field empty return
        if (!em || !pw) return;

        // attempt to login
        attemptLogin(emailField.val(), passwordField.val(), checkoutPage).then(function(result){
            // invalid credentials, display message
            if (result.authenticated == 0) return doFeedback(passwordField, "Invalid email or password");
            // redirect to returned location
            window.location.href = result.redirect_location;
        });
    });
}


/*****************************************************
 * Validates an email address with light validation regex
 * just to ensure that a valid email string has been submitted
 * Regex taken from Jaymon at stackoverflow:
 * https://stackoverflow.com/questions/46155/how-to-validate-an-email-address-in-javascript
 * If email matches the regex an ajax call is made to check whether the email is found on the server
 * if the email is found an email is sent, otherwise an error is displayed
 **************************************************************/
function validateResetEmail(email) {
    const re = /^\S.*@\S+$/;

    if (re.test(email)) {
        // Email ok
        // Remove redundant email error message
        $("#invalid-email-message").html("");
        sendResetEmail(email);
    } else {
        // Email does not match regex
        $("#invalid-email-message").html("Please enter a valid email address");
    }
}

/***********
 * Attempts to send a password reset email
 * backend validates email to ensure an email
 * is being sent to an existing address
 ***********************************/
function sendResetEmail(email) {
    // add a loading indicator to the submit button because mail() load takes a while
    $(document).ajaxStart(function () {
        $('#submit-password-reset').html("Please wait...");
    })

    $.ajax({
        // Ajax parameters
        url: "../php/ajax-handlers/password-reset-handler.php",
        method: "POST",
        data: "email="+email,

    }).done(function(result){
        $('#submit-password-reset').html("submit");
        console.log(result);
        if (result == true) {
            // Success email sent to address
            new Alert(true, "A password reset email has been sent to "+email);
            hideModal("password-reset-modal");
            $("#password-reset-email-input").val("");
        } else {
            // Email not found on server
            new Alert(false, "We were unable to find that email address, please try again");
        }
    });
}


/*********
 * Attempts to login user using ajax call to login handler & page class
 * Response is associative array / object
 *  @authenticated boolean
 *  @redirect_location string
 ****************************************/
function attemptLogin(email, password, checkoutPage) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/processlogin.php",
            method: "POST",
            data: {email: email, password: password, checkoutPage: checkoutPage}
        })
        .done(function(result){
            resolve(JSON.parse(result));
        })
    })
}