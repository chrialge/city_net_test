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
    } else if (inputValue.length > 100) {
        document.getElementById("name_company").style.border = "1px solid red";

        errorNameLower.style.display = "none";
        errorNameGreatest.style.display = "block";
    }
}