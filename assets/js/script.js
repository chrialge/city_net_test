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


function check_address(type) {
    const inputValue = document.getElementById(type).value.trim();
    const errorAddress = document.getElementById(`error_${type}`);

    if (inputValue.length < 3 || inputValue.length > 100) {
        document.getElementById(type).style.border = "1px solid red";
        errorAddress.style.display = "block";
    }
}

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

function hide_error_vat() {
    const inputValue = document.getElementById("vat_number").value.trim();
    const errorVat = document.getElementById("error_vat");
    const regex = /^[0-9]{11}$/;

    if (inputValue.length === 11 && regex.test(inputValue)) {
        document.getElementById("vat_number").style.border = "";
        errorVat.style.display = "";
    }
}

function hide_error_telephone() {
    const inputValue = document.getElementById("telephone").value.trim();
    const errorTelephone = document.getElementById("error_telephone");
    const regex = /^[0-9]/;


    if (regex.test(inputValue) && inputValue.length >= 9 && inputValue.length <= 10) {
        document.getElementById("telephone").style.border = "";
        errorTelephone.style.display = "";
    }
}

function hide_error_email() {
    const inputValue = document.getElementById("email").value.trim();
    const errorEmail = document.getElementById("error_email");
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (regex.test(inputValue) && inputValue.length >= 3 && inputValue.length <= 255) {
        document.getElementById("email").style.border = "";
        errorEmail.style.display = "";
    }
}

function hide_error_address(type) {
    const inputValue = document.getElementById(type).value.trim();
    const errorAddress = document.getElementById(`error_${type}`);

    if (inputValue.length >= 3) {
        document.getElementById(type).style.border = "";
        errorAddress.style.display = "";
    }
}

function hide_error_cap() {
    const inputValue = document.getElementById("cap").value.trim();
    const errorCap = document.getElementById("error_cap");
    const regex = /^[0-9]{5}$/;

    if (regex.test(inputValue)) {
        document.getElementById("cap").style.border = "";
        errorCap.style.display = "";
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


function check_form(e) {
    document.querySelector(".btn_submit").style.display = "none";
    document.querySelector(".btn_load").style.display = "block";

    const province = check_address('province');
    const city = check_address('city');
    const address = check_address('address');
    const cap = check_cap();

    consolre.log(province, city, address, cap);


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
    if (!province || !city || !address || !cap) {
        document.getElementById('error_address_all').style.display = "block";
        e.preventDefault();
        document.querySelector(".btn_submit").style.display = "block";
        document.querySelector(".btn_load").style.display = "none";
    }

}