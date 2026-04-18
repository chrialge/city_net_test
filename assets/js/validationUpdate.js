// utilies
/**
 * function to show or hide password
 * @param {Event} e 
 */
function showPassword(e) {
    const idElement = e.target.parentElement.children[0].getAttribute("id");
    const iconClass = e.target.classList[1];

    if (iconClass === "fa-eye-slash") {
        e.target.classList.remove("fa-eye-slash");
        e.target.classList.add("fa-eye");
        document.getElementById(idElement).setAttribute("type", "password");

    }
    else if (iconClass === "fa-eye") {
        e.target.classList.remove("fa-eye");
        e.target.classList.add("fa-eye-slash");
        document.getElementById(idElement).setAttribute("type", "text");
    }


}
console.log("validationUpdate.js loaded");
// end utilies

// region of update data of access

/**
 * function to check if the name is valid
 * @returns {boolean} true if the name is valid, false otherwise
 */
function check_name() {

    const inputValue = document.getElementById("nome").value.trim();
    const errorNameLower = document.getElementById("error_name_lower");
    const errorNameGreatest = document.getElementById("name_error_greatest");

    if (inputValue.length < 3) {
        document.getElementById("nome").style.borderBottomColor = "red";
        document.getElementById("underline-nome").style.backgroundColor = "red";
        document.getElementById("label-nome").style.color = "red";

        errorNameLower.style.display = "block";
        errorNameGreatest.style.display = "none";

        return false;

    } else if (inputValue.length > 100) {
        document.getElementById("nome").style.borderBottomColor = "red";
        document.getElementById("underline-nome").style.backgroundColor = "red";
        document.getElementById("label-nome").style.color = "red";

        errorNameLower.style.display = "none";
        errorNameGreatest.style.display = "block";

        return false;
    } else {
        return true;
    }
}

/**
 * function to hide the error message for the name
 */
function hide_error_name() {
    const inputValue = document.getElementById("nome").value.trim();
    const errorNameLower = document.getElementById("error_name_lower");
    const errorNameGreatest = document.getElementById("name_error_greatest");

    if (inputValue.length >= 3 && inputValue.length <= 100) {
        document.getElementById("nome").style.borderBottomColor = "";
        document.getElementById("underline-nome").style.backgroundColor = "";
        document.getElementById("label-nome").style.color = "";
        errorNameLower.style.display = "";
        errorNameGreatest.style.display = "";
    }
}

/**
 * function to check the name of the company
 * @returns {boolean} true if the name is valid, false otherwise
 */
function check_cognome() {

    const inputValue = document.getElementById("cognome").value.trim();
    const errorCognomeLower = document.getElementById("error_cognome_lower");
    const errorCognomeGreatest = document.getElementById("error_cognome_greatest");



    if (inputValue.length < 3) {
        document.getElementById("cognome").style.borderBottomColor = "red";
        document.getElementById("underline-cognome").style.backgroundColor = "red";
        document.getElementById("label-cognome").style.color = "red";

        errorCognomeLower.style.display = "block";
        errorCognomeGreatest.style.display = "none";

        return false;

    } else if (inputValue.length > 100) {
        document.getElementById("cognome").style.borderBottomColor = "red";
        document.getElementById("underline-cognome").style.backgroundColor = "red";
        document.getElementById("label-cognome").style.color = "red";

        errorCognomeLower.style.display = "none";
        errorCognomeGreatest.style.display = "block";

        return false;
    } else {
        return true;
    }
}

/**
 * function to hide the error message for the name field
 */
function hide_error_cognome() {
    const inputValue = document.getElementById("cognome").value.trim();
    const errorCognomeLower = document.getElementById("error_cognome_lower");
    const errorCognomeGreatest = document.getElementById("error_cognome_greatest");

    if (inputValue.length >= 3 && inputValue.length <= 100) {
        document.getElementById("cognome").style.borderBottomColor = "";
        document.getElementById("underline-cognome").style.backgroundColor = "";
        document.getElementById("label-cognome").style.color = "";

        errorCognomeLower.style.display = "";
        errorCognomeGreatest.style.display = "";
    }
}


/**
 * function to check if the email is valid
 * @returns {boolean} true if the email is valid, false otherwise
 */
function check_email() {

    const inputValue = document.getElementById("email").value.trim();
    const errorEmail = document.getElementById("error_email");
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!regex.test(inputValue) || inputValue.length < 3 || inputValue.length > 255) {
        document.getElementById("underline-email").style.backgroundColor = "red";
        document.getElementById("label-email").style.color = "red";
        document.getElementById("email").style.borderBottomColor = "red";
        errorEmail.style.display = "block";
        return false;
    } else {
        return true;
    }
}





/**
 * function to hide the error message for the email
 */
function hide_error_email() {
    const inputValue = document.getElementById("email").value.trim();
    const errorEmail = document.getElementById("error_email");
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (regex.test(inputValue) && inputValue.length >= 3 && inputValue.length <= 255) {
        document.getElementById("underline-email").style.backgroundColor = "";
        document.getElementById("label-email").style.color = "";
        document.getElementById("email").style.borderBottomColor = "";
        errorEmail.style.display = "";
    }
}


/**
 * function to check of form access
 * @param {Event} e 
 */
function check_form_access(e) {

    document.querySelector(".btn_submit_access").style.display = "none";
    document.querySelector(".btn_load_access").style.display = "block";
    const password = document.getElementById("password").value.trim();

    if (!check_name()) {
        document.querySelector(".btn_submit_access").style.display = "block";
        document.querySelector(".btn_load_access").style.display = "none";
        // Prevent form submission if validation fails
        e.preventDefault()
    }
    if (password.length === 0) {

        document.getElementById("password").style.border = "1px solid red";
        document.getElementById("error_password").style.display = "block";
        document.querySelector(".btn_submit_access").style.display = "block";
        document.querySelector(".btn_load_access").style.display = "none";
        // Prevent form submission if validation fails
        e.preventDefault()
    }

}

// end region of update data of access

