<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/assets/helper/function.php';
require_once __DIR__ . '/assets/helper/db.php';
require_once __DIR__ . '/assets/models/Company.php';


// var_dump($_POST);

if (isset($_POST['name_company']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['vat_number'])  && isset($_POST['province']) && isset($_POST['city']) && isset($_POST['address']) && isset($_POST['cap'])) {
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


    // Validate the input data
    if (strlen($name_company) < 3 || strlen($name_company) > 100) {
        echo "Nome azienda non valido";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "Le password non combaciano";
        exit;
    }

    if (!preg_match('/^\d{11}$/', $vat_number)) {
        echo "Partita IVA non valida";
        exit;
    }

    if (!empty($telephone) && !preg_match('/^\d{9,10}$/', $telephone)) {
        echo "Numero di telefono non valido";
        exit;
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email non valida";
        exit;
    }

    if (strlen($province) < 3) {
        echo "Provincia non valida";
        exit;
    }

    if (strlen($city) < 3) {
        echo "Città non valida";
        exit;
    }

    if (strlen($address) < 3) {
        echo "Indirizzo non valido";
        exit;
    }

    if (!preg_match('/^\d{5}$/', $cap)) {
        echo "CAP non valido";
        exit;
    }

    $telephone = "+39" . $telephone; // Add the country code to the telephone number
    $address = $province . ", " . $city . ", " . $address . ", " . $cap; // Concatenate the address fields
    $birth_of_day = date('Y-m-d', strtotime($birth_of_day)); // Format the date to YYYY-MM-DD

    // Create a new instance of the Comapny class
    $company = new Company($name_company, $password, $confirm_password, $vat_number, $telephone, $province, $city, $address, $cap, $email, $birth_of_day);

    $connection = DB::connect();
    // Save the company data to the database
    if ($company->save($connection)) {
        echo "Registrazione avvenuta con successo";
    } else {
        echo "Errore durante la registrazione";
    }
} else {
    echo "Dati mancanti";
}
