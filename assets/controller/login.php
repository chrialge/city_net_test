<?php

require_once __DIR__ . '/../helper/db.php';
require_once __DIR__ . '/../helper/Auth.php';


// if the form is submitted
if (!empty($_POST['name_company']) && !empty($_POST['password'])) {

    // save the username and password in variables
    $username = $_POST['name_company'];
    $password = $_POST['password'];

    // connect to the database
    $connection = DB::connect();

    // check if authentication is successful
    if (Auth::check($connection, $username, $password) === true) {

        // Set message to be displayed on the dashboard
        $_SESSION['message'] = 'Login effettuato con successo!';

        // disconnect from the database
        DB::disconnect($connection);

        // redirect to the dashboard page
        header('Location: ../../view/dashboard.php');
        exit;
    } else {

        // if authentication fails, set an error message
        $_SESSION['message'] = 'Username o password errati!';

        // disconnect from the database
        DB::disconnect($connection);

        // redirect to the login page
        header('Location: ../../view/login.php');
    }
} else {

    // if the form is not submitted, redirect to the login page
    header('Location: ../../view/login.php');
    exit;
}
