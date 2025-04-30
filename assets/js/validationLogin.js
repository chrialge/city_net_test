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
 * function to hide the error message of the name
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
 * function to show the password
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
 * function close the message error
 */
function close_message_error() {

    const messageError = document.getElementById("message_error")
    messageError.style.opacity = 0;
    messageError.style.visibility = "hidden";
    setTimeout(() => {
        messageError.style.display = "none";
    }, 300);
}

/**
 * function to check if the form is valid
 * @param {Event} e 
 */
function check_form(e) {

    document.querySelector(".btn_submit").style.display = "none";
    document.querySelector(".btn_load").style.display = "block";
    const password = document.getElementById("password").value.trim();

    if (!check_name()) {
        document.querySelector(".btn_submit").style.display = "block";
        document.querySelector(".btn_load").style.display = "none";
        // Prevent form submission if validation fails
        e.preventDefault()
    }
    if (password.length === 0) {

        document.getElementById("password").style.border = "1px solid red";
        document.getElementById("error_password").style.display = "block";
        document.querySelector(".btn_submit").style.display = "block";
        document.querySelector(".btn_load").style.display = "none";
        // Prevent form submission if validation fails
        e.preventDefault()
    }

}