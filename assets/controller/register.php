<?php

require_once   '../helper/function.php';
require_once '../helper/db.php';
require_once '../models/Utenti.php';
require_once '../helper/Auth.php';



// check if the form gime me compulsory data
if (!empty($_POST['nome']) && !empty($_POST['cognome']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirm_password'])) {

    // Initialize an array to store error messages
    $php_error = [];

    // Get the form data
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    echo $nome;


    // Validate name
    if (strlen($nome) < 3 || strlen($nome) > 100) {
        array_push($php_error, "il nome è troppo corto o troppo lungo");
    }

    // Validate cognome
    if (strlen($cognome) < 3 || strlen($cognome) > 100) {
        array_push($php_error, "il cognome è troppo corto o troppo lungo");
    }

    // check if password is equal to confirm password
    if ($password !== $confirm_password) {
        array_push($php_error, "Le password non combaciano");
    }

    // validate email (optional)
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($php_error, "Email non valida");
    }


    // if there are no errors, proceed with registration
    if (count($php_error) === 0) {


        // Create a new instance of the Comapny class
        $utente = new Utenti($nome, $cognome, $password, $confirm_password, $email);

        $connection = DB::connect();
        // Save the company data to the database
        if ($utente->save($connection)) {


            // Start the session if not already started
            Auth::check($connection, $email, $password);

            // start the session
            session_start();

            // Set a success message in the session
            $_SESSION['message'] = "Registrazione avvenuta con successo";

            // Close the database connection
            DB::disconnect($connection);

            // Redirect to the dashboard page after successful registration
            header('location: ../../view/dashboard.php');
            exit;
        } else {

            // Close the database connection
            DB::disconnect($connection);

            // start the session
            session_start();

            // Set an error message in the session
            $_SESSION['message'] = "Errore durante la registrazione";

            // Redirect to the index page with the error message
            header('Location: ../../index.php');
            exit;
        }
    } else {

        // start the session
        session_start();

        // Set the error messages in the session
        $_SESSION['message'] = $php_error;

        // Redirect to the index page with the error messages
        header('Location: ../../index.php');
        exit;
    }
} else {

    // Initialize an array to store error messages
    $php_error = [];

    // Check if the form is submitted without compulsory data
    if (empty($_POST['nome'])) {
        array_push($php_error, "Il nome dell'azienda manca");
    }
    if (empty($_POST['cognome'])) {
        array_push($php_error, "Il cognome dell'azienda manca");
    }
    if (empty($_POST['email'])) {
        array_push($php_error, "L'email manca");
    }
    if (empty($_POST['password'])) {
        array_push($php_error, "La password manca");
    }


    // if there are errors, redirect to the index page with the error messages
    if (count($php_error) > 0) {

        // start the session
        session_start();

        // Set the error messages in the session
        $_SESSION['message'] = $php_error;

        // Redirect to the index page with the error messages
        header('Location: ../../index.php');
        exit;
    }
}
