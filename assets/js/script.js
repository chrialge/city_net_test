console.log("Hello World");


function check_name() {

    const inputValue = document.getElementById("name_company").value.trim();
    const errorNameLower = document.getElementById("error_name_lower");
    const errorNameGreatest = document.getElementById("name_error_greatest");


    console.log(inputValue, errorNameLower, errorNameGreatest);
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


function hide_error_name() {
    const inputValue = document.getElementById("name_company").value.trim();
    const errorNameLower = document.getElementById("error_name_lower");
    const errorNameGreatest = document.getElementById("name_error_greatest");

    if (inputValue.length >= 3 && inputValue.length <= 100) {
        document.getElementById("name_company").style.border = "";
        errorNameLower.style.display = "none";
        errorNameGreatest.style.display = "none";
    }
}


function check_password() {

    const passwordValue = document.getElementById("password").value.trim();
    const passwordConfirmValue = document.getElementById("confirm_password").value.trim();

    if (passwordConfirmValue === "") {

        // If the confirm password field is empty, fields border will be red
        document.getElementById("password").style.border = "1px solid red";
        document.getElementById("confirm_password").style.border = "1px solid red";

        // If the confirm password field is empty, show an error message
        document.getElementById("error_password").style.display = "";
        document.getElementById("error_password_empty").style.display = "block";

        return false;

    } else if (passwordValue !== passwordConfirmValue) {

        // If the password and confirm password do not match, fields border will be red
        document.getElementById("password").style.border = "1px solid red";
        document.getElementById("confirm_password").style.border = "1px solid red";

        // If the password and confirm password do not match, show an error message
        document.getElementById("error_password").style.display = "block";
        document.getElementById("error_password_empty").style.display = "";

        return false;
    }
}

function hide_error_password() {

    const passwordValue = document.getElementById("password").value.trim();
    const passwordConfirmValue = document.getElementById("confirm_password").value.trim();

    if (passwordValue === passwordConfirmValue) {
        document.getElementById("password").style.border = "";
        document.getElementById("confirm_password").style.border = "";
        document.getElementById("error_password").style.display = "";
        document.getElementById("error_password_empty").style.display = "";
    }

}

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