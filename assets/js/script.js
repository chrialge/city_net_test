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

/**
 * function to close the message error
 */
function close_message_error() {

    const messageError = document.getElementById("message_error")
    messageError.style.opacity = 0;
    messageError.style.visibility = "hidden";

    setTimeout(() => {
        messageError.style.display = "none";
    }, 300);
}

// end utilies

// region check fields

/**
 * function to check the name of the company
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
 * function to check the password and confirm password fields
 * @returns {boolean} true if the password is valid, false otherwise
 */
function check_password() {

    const passwordValue = document.getElementById("password").value.trim();
    const passwordConfirmValue = document.getElementById("confirm_password").value.trim();

    if (passwordConfirmValue === "") {

        // If the confirm password field is empty, fields border will be red
        document.getElementById("password").style.borderBottomColor = "red";
        document.getElementById("confirm_password").style.borderBottomColor = "red";
        document.getElementById("label-confirm-password").style.color = "red";
        document.getElementById("label-password").style.color = "red";
        document.getElementById("underline-password").style.backgroundColor = "red";
        document.getElementById("underline-confirm-password").style.backgroundColor = "red";

        // If the confirm password field is empty, show an error message
        document.getElementById("error_password").style.display = "";
        document.getElementById("error_password_empty").style.display = "block";

        return false;

    } else if (passwordValue !== passwordConfirmValue) {

        // If the password and confirm password do not match, fields border will be red
        document.getElementById("password").style.borderBottomColor = "red";
        document.getElementById("confirm_password").style.borderBottomColor = "red";
        document.getElementById("label-confirm-password").style.color = "red";
        document.getElementById("label-password").style.color = "red";
        document.getElementById("underline-password").style.backgroundColor = "red";
        document.getElementById("underline-confirm-password").style.backgroundColor = "red";

        // If the password and confirm password do not match, show an error message
        document.getElementById("error_password").style.display = "block";
        document.getElementById("error_password_empty").style.display = "";

        return false;
    } else {
        return true;
    }
}


/**
 * function to check the email address
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

// end region check fields

// region hide error message

/**
 * function to hide the error message for the name field
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
 * function to hide the error message for the password field
 */
function hide_error_password() {

    const passwordValue = document.getElementById("password").value.trim();
    const passwordConfirmValue = document.getElementById("confirm_password").value.trim();

    if (passwordValue === passwordConfirmValue) {
        document.getElementById("password").style.borderBottomColor = "";
        document.getElementById("confirm_password").style.borderBottomColor = "";
        document.getElementById("label-confirm-password").style.color = "";
        document.getElementById("label-password").style.color = "";
        document.getElementById("underline-password").style.backgroundColor = "";
        document.getElementById("underline-confirm-password").style.backgroundColor = "";
        document.getElementById("error_password").style.display = "";
        document.getElementById("error_password_empty").style.display = "";
    }

}

/**
 * function to hide the error message for the VAT number field
 */
function hide_error_vat() {
    const inputValue = document.getElementById("vat_number").value.trim();
    const errorVat = document.getElementById("error_vat");
    const regex = /^[0-9]{11}$/;

    if (inputValue.length === 11 && regex.test(inputValue)) {
        document.getElementById("vat_number").style.border = "";
        errorVat.style.display = "";
    }
}

/**
 * function to hide the error message for the telephone number field
 */
function hide_error_telephone() {
    const inputValue = document.getElementById("telephone").value.trim();
    const errorTelephone = document.getElementById("error_telephone");
    const regex = /^[0-9]/;


    if (regex.test(inputValue) && inputValue.length >= 9 && inputValue.length <= 10) {
        document.getElementById("telephone").style.border = "";
        errorTelephone.style.display = "";
    }
}

/**
 * function to hide the error message for the email address field
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
 * function to hide the error message for the address fields (province, city, address)
 * @param {String} type tipology of address (province, city, address) 
 */
function hide_error_address(type) {
    const inputValue = document.getElementById(type).value.trim();
    const errorAddress = document.getElementById(`error_${type}`);

    if (inputValue.length >= 3) {
        document.getElementById(type).style.border = "";
        errorAddress.style.display = "";
    }
}

/**
 * function to hide the error message for the CAP (postal code) field
 */
function hide_error_cap() {
    const inputValue = document.getElementById("cap").value.trim();
    const errorCap = document.getElementById("error_cap");
    const regex = /^[0-9]{5}$/;

    if (regex.test(inputValue)) {
        document.getElementById("cap").style.border = "";
        errorCap.style.display = "";
    }
}

// end region hide error message

/**
 * function to check the form before submission
 * @param {Event} e 
 */
function check_form(e) {

    document.querySelector(".btn_submit").style.display = "none";
    document.querySelector(".btn_load").style.display = "block";

    const province = check_address('province');
    const city = check_address('city');
    const address = check_address('address');
    const cap = check_cap();




    if (!check_name()) {
        e.preventDefault();
        document.querySelector(".btn_submit").style.display = "block";
        document.querySelector(".btn_load").style.display = "none";
    }
    if (!check_password()) {
        e.preventDefault();
        document.querySelector(".btn_submit").style.display = "block";
        document.querySelector(".btn_load").style.display = "none";
    }
    if (!check_vat()) {
        e.preventDefault();
        document.querySelector(".btn_submit").style.display = "block";
        document.querySelector(".btn_load").style.display = "none";
    }
    if (!check_email() && document.getElementById("email").value.trim().length > 0) {
        e.preventDefault();
        document.querySelector(".btn_submit_data").style.display = "block";
        document.querySelector(".btn_load_data").style.display = "none";
    }
    if (!check_telephone() && document.getElementById("telephone").value.trim().length > 0) {
        console.log('ciao')
        e.preventDefault();
        document.querySelector(".btn_submit_data").style.display = "block";
        document.querySelector(".btn_load_data").style.display = "none";
    }
    if (!province || !city || !address || !cap) {
        console.log("ciao")
        document.getElementById('error_address_all').style.display = "block";
        e.preventDefault();
        document.querySelector(".btn_submit").style.display = "block";
        document.querySelector(".btn_load").style.display = "none";
    }

}