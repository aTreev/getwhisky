function prepareUserDetailsPage() {
    const form = $("#user-details-form");
    const firstNameField = $("#first-name");
    const surnameField = $("#surname");
    const emailField = $("#email");

    const passwordField = $("#password");
    const repeatPasswordField = $("#repeat-password");

    passwordField.keyup(function(){
        testPasswordStrength(passwordField);
    });

    form.submit(function(ev){
        ev.preventDefault();
        $(".form-feedback").remove();

        let fn, sn, em = false;
        let pw = true;

        fn = checkFieldEmpty(firstNameField, "Please enter a value for first name", 30);
        sn = checkFieldEmpty(surnameField, "Please enter a value for surname",30);
        em = checkEmail(emailField);
        if (passwordField.val().length != 0) pw = checkPassword(passwordField, repeatPasswordField);

        // if any invalid, return
        if (!fn || !sn || !em || !pw) return;

        // show password confirmation modal
        showModal("password-confirm-modal", true);
        $("#password-confirm-input").val("");
        $("#password-confirm-input").focus();

        // password confirmation submit
        $("#password-confirm-form").submit(function(ev){
            ev.preventDefault();
            $("#invalid-password-message").html("");
            const currentPassword = $("#password-confirm-input").val();
            if (!currentPassword) return;

            // Authenticate the user
            authUserPassword(currentPassword)
            .then(function(result){
                // Guard clause exit with message if authenitcation fails
                if (result.user_authenticated == false) return $("#invalid-password-message").html("Incorrect password");
               
                hideModal("password-confirm-modal");
                
                // Authenticated, update details
                updateUserDetails(firstNameField.val(), surnameField.val(), emailField.val(), passwordField.val())
                .then(function(result){
                    if (result.messages.emails) return doFeedback(emailField, result.messages.email);
                    new Alert(true, result.messages)
                });
            });
        });
        
        //updateUserDetails(firstNameField.val(), surnameField.val(), emailField.val(), passwordField.val())
    });
}


function authUserPassword(currentPassword) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/user-details-handler.php",
            method: "POST",
            data: {function: 1, currentPassword: currentPassword}
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}


function updateUserDetails(firstName, surname, email, newPassword) {
    return new Promise(function(resolve){
        $.ajax({
            url: "../php/ajax-handlers/user-details-handler.php",
            method: "POST",
            data: {function: 2, firstName: firstName, surname: surname, email: email, newPassword, newPassword}
        })
        .done(function(result){
            resolve(JSON.parse(result));
        });
    });
}