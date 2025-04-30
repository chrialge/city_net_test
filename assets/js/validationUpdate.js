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

// end utilies

// region of update data of access

/**
 * function to check if the name is valid
 * @returns {boolean} true if the name is valid, false otherwise
 */
function check_name() {

    const inputValue = document.getElementById("name_company").value.trim();
    const errorNameLower = document.getElementById("error_name_lower");
    const errorNameGreatest = document.getElementById("name_error_greatest");

    if (inputValue.length < 3) {
        document.getElementById("name_company").style.border = "1px solid red";

        errorNameLower.style.display = "block";
        errorNameGreatest.style.display = "none";

        return false;

    } else if (inputValue.length > 100) {
        document.getElementById("name_company").style.border = "1px solid red";

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
    const inputValue = document.getElementById("name_company").value.trim();
    const errorNameLower = document.getElementById("error_name_lower");
    const errorNameGreatest = document.getElementById("name_error_greatest");

    if (inputValue.length >= 3 && inputValue.length <= 100) {
        document.getElementById("name_company").style.border = "";
        errorNameLower.style.display = "";
        errorNameGreatest.style.display = "";
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

// region updatte data of company

/**
 * function to check if the VAT number is valid
 * @returns {boolean} true if the VAT number is valid, false otherwise
 */
function check_vat() {
    const inputValue = document.getElementById("vat_number").value.trim();
    const errorVat = document.getElementById("error_vat");
    const regex = /^[0-9]/;


    if (inputValue.length !== 11 || !regex.test(inputValue)) {
        document.getElementById("vat_number").style.border = "1px solid red";
        errorVat.style.display = "block";
        return false;
    } else {
        return true;
    }
}

/**
 * function to check if the telephone number is valid
 * @returns {boolean} true if the telephone number is valid, false otherwise
 */
function check_telephone() {
    const inputValue = document.getElementById("telephone").value.trim();
    const errorTelephone = document.getElementById("error_telephone");
    const regex = /^[0-9]/;


    if (!regex.test(inputValue) || inputValue.length < 9 || inputValue.length > 10) {
        document.getElementById("telephone").style.border = "1px solid red";
        errorTelephone.style.display = "block";
        return false;
    } else {
        return true;
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
        document.getElementById("email").style.border = "1px solid red";
        errorEmail.style.display = "block";
        return false;
    } else {
        return true;
    }
}

/**
 * function to check if the address is valid
 * @param {String} type tipology of address (province, city, address)
 * @returns {boolean} true if the address is valid, false otherwise
 */
function check_address(type) {
    const inputValue = document.getElementById(type).value.trim();
    const errorAddress = document.getElementById(`error_${type}`);

    if (inputValue.length < 3 || inputValue.length > 100) {
        document.getElementById(type).style.border = "1px solid red";
        errorAddress.style.display = "block";

        return false;
    } else {
        return true;
    }
}

/**
 * function to check if the cap is valid
 * @returns {boolean} true if the cap is valid, false otherwise
 */
function check_cap() {
    const inputValue = document.getElementById("cap").value.trim();
    const errorCap = document.getElementById("error_cap");
    const regex = /^[0-9]{5}$/;

    if (!regex.test(inputValue)) {
        document.getElementById("cap").style.border = "1px solid red";
        errorCap.style.display = "block";
        return false;
    } else {
        return true;
    }
}

/**
 * function to hide the error message for the VAT number
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
 * function to hide the error message for the telephone number
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
 * function to hide the error message for the email
 */
function hide_error_email() {
    const inputValue = document.getElementById("email").value.trim();
    const errorEmail = document.getElementById("error_email");
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (regex.test(inputValue) && inputValue.length >= 3 && inputValue.length <= 255) {
        document.getElementById("email").style.border = "";
        errorEmail.style.display = "";
    }
}


/**
 * function to hide the error message for the address
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
 * function to hide the error message for the cap
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

/**
 * function to check if the form data is valid
 * @param {Event} e 
 */
function check_form_data(e) {
    document.querySelector(".btn_submit_data").style.display = "none";
    document.querySelector(".btn_load_data").style.display = "block";

    const province = check_address('province');
    const city = check_address('city');
    const address = check_address('address');
    const cap = check_cap();


    if (!check_vat()) {
        e.preventDefault();
        document.querySelector(".btn_submit_data").style.display = "block";
        document.querySelector(".btn_load_data").style.display = "none";
    }
    if (!check_email() && document.getElementById("email").value.trim().length > 0) {
        e.preventDefault();
        document.querySelector(".btn_submit_data").style.display = "block";
        document.querySelector(".btn_load_data").style.display = "none";
    }
    if (!check_telephone() && document.getElementById("telephone").value.trim().length > 0) {
        e.preventDefault();
        document.querySelector(".btn_submit_data").style.display = "block";
        document.querySelector(".btn_load_data").style.display = "none";
    }
    if (!province || !city || !address || !cap) {
        document.getElementById('error_address_all').style.display = "block";
        e.preventDefault();
        document.querySelector(".btn_submit_data").style.display = "block";
        document.querySelector(".btn_load_data").style.display = "none";
    }

}

// end region of update data of company