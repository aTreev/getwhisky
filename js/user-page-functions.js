function prepareUserPage() {
    const xmlHttp = new XMLHttpRequest();
    if ($("#resend-validation")) {
        $("#resend-validation").click(function() {
            resendValidationEmail(xmlHttp);
        }); 
    }
    
}


function resendValidationEmail(xmlHttp) {
    $(document)
    .ajaxStart(function () {
        $('#resend-validation').html("<img style='width: 32px;'src='/assets/loader-button.gif'>");
    })
    .ajaxStop(function () {
        $('#resend-validation').html("resend validation email");
    });

    $.ajax({
        url: "../php/ajax-handlers/resend-validation-email.php",
        method: "POST",
        statusCode: {
            404: function() {
              new Alert(false,"There was an error with the server, please try again");
            }
          }
    }).done(function(result){
        // parse to allow property access
        result = JSON.parse(result);
        sendConfirmationNotification(result.sent, result.address);
        
    });
}

function sendConfirmationNotification(isSuccessful, emailAddress) {
    if (isSuccessful) return new Alert(isSuccessful, "A verification email has been sent to "+emailAddress+"!");
    return new Alert(isSuccessful, "There was an error sending an email, please contact info@getwhisky if issue persists")
}
    