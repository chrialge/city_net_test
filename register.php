<?php

require_once __DIR__ . '/assets/helper/function.php';
require_once __DIR__ . '/assets/helper/db.php';
require_once __DIR__ . '/assets/models/Company.php';
require_once __DIR__ . '/assets/helper/Auth.php';


// check if the form gime me compulsory data
if (!empty($_POST['name_company']) && !empty($_POST['password']) && !empty($_POST['confirm_password']) && !empty($_POST['vat_number'])  && !empty($_POST['province']) && !empty($_POST['city']) && !empty($_POST['address']) && !empty($_POST['cap'])) {

    // Initialize an array to store error messages
    $php_error = [];

    // Get the form data
    $name_company = $_POST['name_company'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $vat_number = $_POST['vat_number'];
    $telephone = $_POST['telephone'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $cap = $_POST['cap'];
    $email = $_POST['email'];
    $birth_of_day = $_POST['birth_of_day'];


    // Validate name_company
    if (strlen($name_company) < 3 || strlen($name_company) > 100) {
        array_push($php_error, "Nome azienda non valido");
    }

    // check if password is equal to confirm password
    if ($password !== $confirm_password) {
        array_push($php_error, "Le password non combaciano");
    }

    // validate vat_number
    if (!preg_match('/^\d{11}$/', $vat_number)) {
        array_push($php_error, "Partita IVA non valida");
    }

    // validate telephone number (optional)
    if (!empty($telephone) && !preg_match('/^\d{9,10}$/', $telephone)) {
        array_push($php_error, "Numero di telefono non valido");
    }

    // validate email (optional)
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($php_error, "Email non valida");
    }

    // validate province
    if (strlen($province) < 3) {
        array_push($php_error, "Provincia non valida");
    }

    // validate city
    if (strlen($city) < 3) {
        array_push($php_error, "Città non valida");
    }

    // validate address
    if (strlen($address) < 3) {
        array_push($php_error, "Indirizzo non valido");
    }

    // validate cap
    if (!preg_match('/^\d{5}$/', $cap)) {
        array_push($php_error, "CAP non valido");
    }


    // if there are no errors, proceed with registration
    if (count($php_error) === 0) {
        // Add the country code to the telephone number
        $telephone = "+39" . $telephone;

        // Concatenate the address fields
        $address = $province . ", " . $city . ", " . $address . ", " . $cap;

        // Validate the date format (YYYY-MM-DD)
        $birth_of_day = date('Y-m-d', strtotime($birth_of_day));

        // Create a new instance of the Comapny class
        $company = new Company($name_company, $password, $confirm_password, $vat_number, $telephone, $province, $city, $address, $cap, $email, $birth_of_day);

        $connection = DB::connect();
        // Save the company data to the database
        if ($company->save($connection)) {


            // Start the session if not already started
            Auth::check($connection, $name_company, $password);

            // start the session
            session_start();

            // Set a success message in the session
            $_SESSION['message'] = "Registrazione avvenuta con successo";

            // Close the database connection
            DB::disconnect($connection);

            // Redirect to the dashboard page after successful registration
            header('location: view/dashboard.php');
            exit;
        } else {

            // Close the database connection
            DB::disconnect($connection);

            // start the session
            session_start();

            // Set an error message in the session
            $_SESSION['message'] = "Errore durante la registrazione";

            // Redirect to the index page with the error message
            header('Location: ./index.php');
            exit;
        }
    } else {

        // start the session
        session_start();

        // Set the error messages in the session
        $_SESSION['message'] = $php_error;

        // Redirect to the index page with the error messages
        header('Location: ./index.php');
        exit;
    }
} else {

    // Initialize an array to store error messages
    $php_error = [];

    // Check if the form is submitted without compulsory data
    if (empty($_POST['name_company'])) {
        array_push($php_error, "Il nome dell'azienda manca");
    }
    if (empty($_POST['password'])) {
        array_push($php_error, "La password manca");
    }
    if (empty($_POST['vat_number'])) {
        array_push($php_error, "La partita IVA manca");
    }
    if (empty($_POST['province'])) {
        array_push($php_error, "La provincia manca");
    }
    if (empty($_POST['city'])) {
        array_push($php_error, "La città manca");
    }
    if (empty($_POST['address'])) {
        array_push($php_error, "L'indirizzo manca");
    }
    if (empty($_POST['cap'])) {
        array_push($php_error, "Il CAP manca");
    }

    // if there are errors, redirect to the index page with the error messages
    if (count($php_error) > 0) {

        // start the session
        session_start();

        // Set the error messages in the session
        $_SESSION['message'] = $php_error;

        // Redirect to the index page with the error messages
        header('Location: ./index.php');
        exit;
    }
}
