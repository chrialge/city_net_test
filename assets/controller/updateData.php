<?php

require_once __DIR__ . '/../helper/db.php';

// if the form is submitted
if (!empty($_POST['vat_number']) && !empty($_POST['birth_of_day']) && !empty($_POST['province']) && !empty($_POST['address']) && !empty($_POST['city']) && !empty($_POST['cap'])) {

    // Initialize an array to store error messages
    $error_php = [];

    // Get the form data
    $vat_number = $_POST['vat_number'];
    $telephone = "+39" . $_POST['telephone'];
    $email = $_POST['email'];

    // concatenate the address fields
    $address = $_POST['province'] . ", " . $_POST['city'] . ", " . $_POST['address'] . ", " . $_POST['cap'];

    // Validate the date format (YYYY-MM-DD)
    $birth_of_day = date('Y-m-d', strtotime($_POST['birth_of_day']));

    // if the email is not empty, validate it
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        array_push($error_php, 'Email non valida!');
    }

    // if the telephone is not empty, validate it
    if (!empty($_POST['telephone']) && !preg_match('/^\d{9,10}$/', $_POST['telephone'])) {
        array_push($error_php, 'Numero di telefono non valido!');
    }

    // if doesn't error php
    if (count($error_php) === 0) {

        // start the session
        session_start();

        // get the user id from the session
        $id = $_SESSION['userId'];

        // connect to the database
        $mysqli = DB::connect();

        // query update the data
        $query = "UPDATE `companies` SET `vat_number` = '$vat_number', `telephone` = '$telephone', `email` = '$email', `birth_of_day` = '$birth_of_day', `address` = '$address' WHERE `id` = '$id'";

        // execute the query
        $result = $mysqli->query($query);

        // message success
        $_SESSION['message'] = "Modifica dei dati dell'azienda avvenuta con successo!";

        // disconnect from the database
        DB::disconnect($mysqli);

        // redirect to the Setting page
        header('Location: ../../view/setting.php');
        exit;
    } else {

        // start the session
        session_start();

        // set the error message in the session
        $_SESSION['error_array'] = $error_php;

        // redirect to the Setting page
        header('Location: ../../view/setting.php');
        exit;
    }
} else {

    // Start the session
    session_start();

    // Set an error message in the session
    $_SESSION['error'] = 'Errore Mancano i dati obbligatori!';

    // redirect to the Setting page
    header('Location: ../../view/setting.php');
    exit;
}
