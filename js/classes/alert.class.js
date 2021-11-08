/*************
 * this code is trash because I'm terrible at JavaScript
 * Class alert
 *  Takes in a successState and message
 *  Adds the passed in message to the alert and customizes 
 *  the alert depending on whether successState is true or false.
 *  returnAlert() has to be called to retrieve the alert.
 *  initializeAlert is then called to provide the fancy functionality
 *  to the alert.
 */
class Alert {

    constructor(successState, message) {
        this.successState = successState;
        this.messageText = message;
        this.alert = this.createAlert();
        this.initializeAlert();
    }
    
    createAlert() {
        let alertContainer = document.createElement("div");
        alertContainer.classList.add("alert");
        alertContainer.classList.add("ignore-overlay");

        let alertIcon = document.createElement("i");

        if (this.successState == true) {
            alertContainer.classList.add("success");
            alertIcon.classList.add("message-icon", "fas", "fa-check-circle");
        } else {
            alertContainer.classList.add("error");
            alertIcon.classList.add("message-icon", "fas", "fa-exclamation-circle");
        }
        
        let messageContainer = document.createElement("div");
        messageContainer.classList.add("alert-message-container");


        let message = document.createElement("p");
        message.innerHTML = this.messageText;

        messageContainer.insertAdjacentElement("beforeend",alertIcon);
        messageContainer.insertAdjacentElement("beforeend",message);

        let closeButtonContainer = document.createElement("div");
        closeButtonContainer.classList.add("close-alert-container");

        let closeIcon = document.createElement("i");
        closeIcon.classList.add("fas", "fa-times");
        
        closeButtonContainer.insertAdjacentElement("afterbegin", closeIcon);

        alertContainer.insertAdjacentElement("beforeend", messageContainer);
        alertContainer.insertAdjacentElement("beforeend", closeButtonContainer);
        return alertContainer;
    }

    returnAlert() {
        return this.alert;
    }

    initializeAlert() {
        if ($(".alert")) {
            $(".alert").remove();
        }
        let theAlert = this.alert;
        document.querySelector("main").insertAdjacentElement("afterbegin", theAlert);
        setTimeout(() => {
            theAlert.style.transform = "translateX(0%)";
            theAlert.style.marginRight = "10px";

        }, 50)

        let outTimeout = setTimeout(() => {
            theAlert.style.transform = "translateX(100%)";
            theAlert.style.marginRight = "0px";
            setTimeout(() =>{
                theAlert.remove();
            }, 4000)
        }, 5000);

        theAlert.addEventListener("mouseover", function() {
            clearTimeout(outTimeout);
            
        });

        theAlert.addEventListener("mouseleave", function() {
            setTimeout(() => {
                theAlert.style.transform = "translateX(100%)";
                theAlert.style.marginRight = "0px";
                setTimeout(() =>{
                    theAlert.remove();
                }, 4000);
            }, 5000);
        });
        
        theAlert.children[1].addEventListener("click", function() {
            theAlert.remove();
        })
    }
}