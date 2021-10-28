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
    }
    
    createAlert() {
        let alertContainer = document.createElement("div");
        alertContainer.classList.add("alert");

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
        message.innerText = this.messageText;

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

    initializeAlert(fromPageAlert) {
        //passing the object back in like this instead of using an ID has the advantage
        //of methods always having a reference to that particular object.
        //this prevents the need for additional code to check if an alert is already on the page.
        setTimeout(() => {
            fromPageAlert.style.transform = "translateX(0%)";
        }, 1000)

        let outTimeout = setTimeout(() => {
            fromPageAlert.style.transform = "translateX(100%)";
            setTimeout(() =>{
                fromPageAlert.remove();
            }, 4000)
        }, 5000);

        fromPageAlert.addEventListener("mouseover", function() {
            clearTimeout(outTimeout);
            
        });

        fromPageAlert.addEventListener("mouseleave", function() {
            setTimeout(() => {
                fromPageAlert.style.transform = "translateX(100%)";
                setTimeout(() =>{
                    fromPageAlert.remove();
                }, 4000);
            }, 5000);
        });
        
        fromPageAlert.children[1].addEventListener("click", function() {
            fromPageAlert.remove();
        })
    }
}