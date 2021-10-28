function prepareUserPage() {
    const xmlHttp = new XMLHttpRequest();
    if (document.querySelector("#resend-validation")) {
        document.querySelector("#resend-validation").addEventListener("click", function() {
            resendValidationEmail(xmlHttp);
        }); 
    }
    
}


function resendValidationEmail(xmlHttp) {
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let response = JSON.parse(this.responseText);
            sendConfirmationNotification(response.sent, response.address);
            
        }
    }
    // Send request, requests must be sent outside of the onreadystatechange
    xmlHttp.open("POST", "../php/resend-validation-email.php", true);
    xmlHttp.send();
}

function sendConfirmationNotification(isSuccessful, emailAddress) {
    let message = "A verification email has been sent to "+emailAddress+"!";
    let alertObj = new Alert(isSuccessful, message);
    let alert = alertObj.returnAlert();
    document.querySelector("main").insertAdjacentElement("afterbegin", alert);
    alertObj.initializeAlert(alert);
}